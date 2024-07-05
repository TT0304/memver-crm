<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mails\Email;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Email\EmailRepository;
use App\Repositories\Email\AttachmentRepository;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    /**
     * LeadRepository object
     *
     * @var \App\Email\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * EmailRepository object
     *
     * @var \App\Email\Repositories\EmailRepository
     */
    protected $emailRepository;

    /**
     * AttachmentRepository object
     *
     * @var \App\Email\Repositories\AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Lead\Repositories\LeadRepository  $leadRepository
     * @param \App\Email\Repositories\EmailRepository  $emailRepository
     * @param \App\Email\Repositories\AttachmentRepository  $attachmentRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        EmailRepository $emailRepository,
        AttachmentRepository $attachmentRepository,
    ) {
        $this->leadRepository = $leadRepository;

        $this->emailRepository = $emailRepository;

        $this->attachmentRepository = $attachmentRepository;


        request()->request->add(['entity_type' => 'emails']);
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('mail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (! request('route')) {
            return Inertia::location(route('mail.index', ['route' => 'inbox']));
        }

        // if (!bouncer()->hasPermission('mail.' . request('route'))) {
        //     abort(401, 'This action is unauthorized');
        // }

        switch (request('route')) {
            case 'compose':
                abort_if(Gate::denies('mail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

                $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
                    'entity_type' => 'emails',
                ]);
                $currencyCode = core()->currencySymbol(config('app.currency'));
                return Inertia::render('dashboards/mail/compose', [
                    'customAttributes' => $customAttributes,
                    'currencyCode' => $currencyCode,
                ]);
            default:
                if (request()->ajax()) {
                    return app(\App\DataGrids\Mail\EmailDataGrid::class)->toJson();
                }
                return Inertia::render('dashboards/mail/index', []);
        }
        
    }

    /**
     * Display a resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $email = $this->emailRepository
            ->with(['emails', 'attachments', 'emails.attachments', 'lead', 'person'])
            ->findOrFail(request('id'));
            
        $currentUser = auth()->user();
        // if ($currentUser->view_permission == 'individual') {            
        //     $results = $this->leadRepository->findWhere([
        //         ['id', '=', $email->lead_id],
        //         ['user_id', '=', $currentUser->id],
        //     ]);
        // } elseif ($currentUser->view_permission == 'group') {
        //     $userIds = app('\App\Repositories\User\UserRepository')->getCurrentUserGroupsUserIds();

        //     $results = $this->leadRepository->findWhere([
        //         ['id', '=', $email->lead_id],
        //         ['user_id', 'IN', $userIds],
        //     ]);
        // } elseif ($currentUser->view_permission == 'global') {
            $results = $this->leadRepository->findWhere([
                ['id', '=', $email->lead_id],
            ]);
        // }
           
        if (empty($results->toArray())) {
            unset($email->lead_id);
        }

        if (request('route') == 'draft') {
            abort_if(Gate::denies('mail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
                'entity_type' => 'emails',
            ]);
            $currencyCode = core()->currencySymbol(config('app.currency'));
            return Inertia::render('dashboards/mail/compose', [
                'customAttributes' => $customAttributes,
                'currencyCode' => $currencyCode,
                'email' => $email
            ]);
        } else {
            abort_if(Gate::denies('mail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         
            // $html = $email->lead_id
            //     ? view('Components.attributes.view', [
            //         'customAttributes' => app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            //                 'entity_type' => 'leads',
            //             ]),
            //             'entity'       => $email->lead,
            //         ])->render() 
            //     : '';
            $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
                'entity_type' => 'leads',
                'quick_add'   => 1
            ]);
            $organizationAttribute = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
                'entity_type' => 'persons',
                'quick_add'   => 1
            ]);
            $organizationAttributeone = app('App\Repositories\Attribute\AttributeRepository')->findOneWhere([
                'entity_type' => 'persons',
                'code'        => 'organization_id'
            ]);
            
            $currencyCode = core()->currencySymbol(config('app.currency'));
            return Inertia::render('dashboards/mail/view', [
                'html' => '',
                'email' => $email,
                'customAttributes' => $customAttributes,
                'organizationAttribute' => $organizationAttribute,
                'currencyCode' => $currencyCode,
                'organizationAttributeone' => $organizationAttributeone
            ]);
        }
    }

    
    public function filterReplyTo(array $reply_to) {
        // Filter out null values
        $filtered = array_filter($reply_to, function($value) {
            return !is_null($value);
        });
    
        // Reindex the array to ensure the keys are sequential
        return array_values($filtered);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'reply_to' => 'required|array|min:1',
            'subject'    => 'required',
            'reply'    => 'required',
        ]);
        
        $uniqueId = time() . '@' . config('mail.domain');

        $referenceIds = [];
        if ($parentId = request('parent_id')) {
            $parent = $this->emailRepository->findOrFail($parentId);

            $referenceIds = $parent->reference_ids ?? [];
        }
        
        $email = $this->emailRepository->create(array_merge(request()->all(), [
            'source'        => 'web',
            'reply_to'      => $this->filterReplyTo(request('reply_to')),
            'from'          => auth()->user()->email,
            'user_type'     => 'admin',
            'folders'       => request('is_draft') ? ['draft'] : ['outbox'],
            'name'          => auth()->user()->name,
            'unique_id'     => $uniqueId,
            'message_id'    => $uniqueId,
            'reference_ids' => array_merge($referenceIds, [$uniqueId]),
            'user_id'       => auth()->user()->id,
            'cc'       => request('cc')&&count(array_filter(request('cc'), 'strlen')) ? request('cc') : null,
            'bcc'       => request('bcc')&&count(array_filter(request('bcc'), 'strlen')) ? request('bcc') : null,
        ]));

        if (!request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['inbox', 'sent']
                ], $email->id);
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }

        if (request('is_draft')) {
            session()->flash('success', trans('app.mail.saved-to-draft'));
            
            return Inertia::location(route('mail.index', ['route'   => 'draft']));
        }

        session()->flash('success', trans('app.mail.create-success'));
        
        return Inertia::location(route('mail.index', ['route'   => 'inbox']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();

        if (!is_null(request('is_draft'))) {
            $data['folders'] = request('is_draft') ? ['draft'] : ['outbox'];
        }

        $email = $this->emailRepository->update($data, request('id') ?? $id);

        if (!is_null(request('is_draft')) && !request('is_draft')) {
            try {
                Mail::send(new Email($email));

                $this->emailRepository->update([
                    'folders' => ['inbox', 'sent']
                ], $email->id);
            } catch (\Exception $e) {
            }
        }

        if (!is_null(request('is_draft'))) {
            if (request('is_draft')) {
                session()->flash('success', trans('app.mail.saved-to-draft'));
                return Inertia::location(route('mail.index', ['route'   => 'draft']));
            } else {
                session()->flash('success', trans('app.mail.create-success'));
                return Inertia::location(route('mail.index', ['route'   => 'inbox']));
            }
        }

        if (request()->ajax()) {
            $response = [
                'message' => trans('app.mail.update-success'),
            ];
            
            if (request('lead_id')) {
                // $response['html'] = view('common.custom-attributes.view', [
                //     'customAttributes' => app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                //         'entity_type' => 'leads',
                //     ]),
                //     'entity'           => $this->leadRepository->find(request('lead_id')),
                // ])->render();
            }

            return response()->json($response);
        } else {
            session()->flash('success', trans('app.mail.update-success'));
            return Inertia::location(url()->previous());
        }
    }

    /**
     * Run process inbound parse email
     *
     * @return \Illuminate\Http\Response
     */
    public function inboundParse()
    {
        $emailContent = file_get_contents(base_path('email.txt'));

        $this->emailRepository->processInboundParseMail($emailContent);

        return response()->json([], 200);
    }

    /**
     * Download file from storage
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function download($id)
    {
        $attachment = $this->attachmentRepository->findOrFail($id);

        return Storage::download($attachment->path);
    }

    /**
     * Mass Update the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        foreach (request('rows') as $emailId) {
            Event::dispatch('email.update.before', $emailId);

            $this->emailRepository->update([
                'folders' => request('folders'),
            ], $emailId);

            Event::dispatch('email.update.after', $emailId);
        }

        return response()->json([
            'message' => trans('app.mail.mass-update-success'),
        ]);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('mail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $email = $this->emailRepository->findOrFail($id);

        try {
            Event::dispatch('email.' . request('type') . '.before', $id);

            $parentId = $email->parent_id;

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $id);
            } else {
                $this->emailRepository->delete($id);
            }

            Event::dispatch('email.' . request('type') . '.after', $id);

            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('app.mail.delete-success'),
                ], 200);
            } else {
                session()->flash('success', trans('app.mail.delete-success'));

                if ($parentId) {
                    return Inertia::location(url()->previous());
                } else {
                    return Inertia::location(route('mail.index', ['route'   => 'inbox']));
                }
            }
        } catch (\Exception $exception) {
            if (request()->ajax()) {
                return response()->json([
                    'message' => trans('app.mail.delete-failed'),
                ], 400);
            } else {
                session()->flash('error', trans('app.mail.delete-failed'));
                return Inertia::location(url()->previous());
            }
        }
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $emailId) {
            Event::dispatch('email.' . request('type') . '.before', $emailId);

            if (request('type') == 'trash') {
                $this->emailRepository->update([
                    'folders' => ['trash'],
                ], $emailId);
            } else {
                $this->emailRepository->delete($emailId);
            }

            Event::dispatch('email.' . request('type') . '.after', $emailId);
        }

        return response()->json([
            'message' => trans('app.mail.destroy-success'),
        ]);
    }
}
