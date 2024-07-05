<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Contact\PersonRepository;
use App\Repositories\Lead\MemberRepository;
use App\Repositories\Lead\PipelineRepository;
use App\Repositories\Lead\StageRepository;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Http\Requests\Lead\LeadForm;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\Common;
use App\DataStructure\EmailTemplate\Models\EmailTemplate;
use App\DataStructure\Product\Models\Product;
use App\DataStructure\Contact\Models\Organization;
use App\DataStructure\Attribute\Models\Attribute;
use App\DataStructure\Lead\Models\Source;
use App\DataStructure\Lead\Models\Type As LeadType;
use App\Helpers\Workflow\Entity\Lead;

class LeadsController extends Controller
{
    /**
     * Lead repository instance.
     *
     * @var \App\Repositories\Lead\LeadRepository
     */
    protected $leadRepository;

    /**
     * Pipeline repository instance.
     *
     * @var \App\Repositories\Lead\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Stage repository instance.
     *
     * @var \App\Repositories\Lead\StageRepository
     */
    protected $stageRepository;

    /**
     * Member repository instance.
     *
     * @var \App\Repositories\Lead\MemberRepository
     */
    protected $personRepository;

    /**
     * Member repository instance.
     *
     * @var \App\Repositories\Lead\MemberRepository
     */
    protected $memberRepository;

    protected $emailTemplateRepository;

    protected $entity;

    
    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Lead\LeadRepository  $leadRepository
     * @param \App\Repositories\Lead\PipelineRepository  $pipelineRepository
     * @param \App\Repositories\Lead\StageRepository  $stageRepository
     * @param \App\Repositories\Lead\MemberRepository  $memberRepository
     *
     * @return void
     */
    public function __construct(
        LeadRepository $leadRepository,
        PipelineRepository $pipelineRepository,
        StageRepository $stageRepository,
        PersonRepository $personRepository,
        MemberRepository $memberRepository,
        EmailTemplateRepository $emailTemplateRepository,
        Lead $entity
    ) {
        $this->leadRepository = $leadRepository;

        $this->pipelineRepository = $pipelineRepository;

        $this->stageRepository = $stageRepository;

        $this->personRepository = $personRepository;

        $this->memberRepository = $memberRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->entity = $entity;

        request()->request->add(['entity_type' => 'leads']);
    }

    public function leads_index_page()
    {
        // $totalSources = Source::all();
        // $totalLeadTypes = LeadType::all();
        // $totalProducts = Product::all();
        // dd($totalSources, $totalLeadTypes);

        $user = auth()->user();
        $permissions = $user->permissions;
        \Log::info($user);
        \Log::info($permissions);
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (!$pipelineId = request('pipeline_id')) {
            $pipelineId = $this->pipelineRepository->getDefaultPipeline()->id;
        }
        $pipelines = $this->pipelineRepository->all();
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/leads/index', [
            'pipelineId' => $pipelineId,
            'pipelines' => $pipelines,
            'currencyCode' => $currencyCode,
        ]);
    }

    public function lead_view_page($id)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead = $this->leadRepository->with(['tags', 'person', 'person.organization', 'products', 'stage', 'pipeline', 'pipeline.stages'])->findOrFail($id);

        $currentUser = auth()->user();

        // if ($currentUser->view_permission != 'global') {
        //     if ($currentUser->view_permission == 'group') {
                // $userIds = app('\App\Repositories\User\UserRepository')->getCurrentUserGroupsUserIds();

                // if (! in_array($lead->user_id, $userIds)) {
                //     return redirect()->route('dashboards.leads.index');
                // }
        //     } else {
        //         if ($lead->user_id != $currentUser->id) {
        //             return redirect()->route('dashboards.leads.index');
        //         }
        //     }
        // }
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'leads',
            'quick_add'   => 1
        ]);
        $organizationAttribute = app('App\Repositories\Attribute\AttributeRepository')->findOneWhere([
            'entity_type' => 'persons',
            'code'        => 'organization_id'
        ]);
        $customAttributes->transform(function ($attribute) {
            $options = $attribute->lookup_type ? app('App\Repositories\Attribute\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
                : $attribute->options()->orderBy('sort_order')->get();
            $attribute['options'] = $options;
            return $attribute;
        });
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $activities = app('\App\Repositories\Lead\LeadRepository')->getAllActivities($lead->id);
        $quotes = $lead->quotes()->with(['person', 'user'])->get();
        return Inertia::render('dashboards/leads/view', [
            'lead' => $lead,
            'person' => $lead->person,
            'personOrganization' => $lead->person->organization,
            'products' => $lead->products,
            'customAttributes' => $customAttributes,
            'organizationAttribute' => $organizationAttribute,
            'currencyCode' => $currencyCode,
            'currentStage' => $lead->stage,
            'customStages' => $lead->pipeline->stages,
            'activities' => $activities,
            'quotes' => $quotes,
        ]);
    }

    

    public function create_lead_page()
    {
        abort_if(Gate::denies('lead_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $stage_id = request('stage_id');
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'leads',
            'quick_add'   => 1
        ]);
        $organizationAttribute = app('App\Repositories\Attribute\AttributeRepository')->findOneWhere([
            'entity_type' => 'persons',
            'code'        => 'organization_id'
        ]);
        $customAttributes->transform(function ($attribute) {
            $options = $attribute->lookup_type ? app('App\Repositories\Attribute\AttributeRepository')->getLookUpOptions($attribute->lookup_type)
                : $attribute->options()->orderBy('sort_order')->get();
            $attribute['options'] = $options;
            return $attribute;
        });
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/leads/create', [
            'customAttributes' => $customAttributes,
            'organizationAttribute' => $organizationAttribute,
            'currencyCode' => $currencyCode,
            'stage_id' => $stage_id ?? 1,
        ]);
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        if (request('view_type')) {
            return app(\App\DataGrids\Lead\LeadDataGrid::class)->toJson();
        } else {
            $createdAt = request('created_at') ?? null;

            if ($createdAt && isset($createdAt["bw"])) {
                $createdAt = explode(",", $createdAt["bw"]);

                $createdAt[0] .= ' 00:01';

                $createdAt[1] = $createdAt[1]
                    ? $createdAt[1] . ' 23:59'
                    : Carbon::now()->format('Y-m-d 23:59');
            } else {
                $createdAt = null;
            }

            if (request('pipeline_id')) {
                $pipeline = $this->pipelineRepository->find(request('pipeline_id'));
            } else {
                $pipeline = $this->pipelineRepository->getDefaultPipeline();
            }

            $leads = $this->leadRepository->getLeads($pipeline->id, request('search') ?? '', $createdAt)->toArray();

            $totalCount = [];

            foreach ($leads as $key => $lead) {
                $totalCount[$lead['status']] = ($totalCount[$lead['status']] ?? 0) + (float) $lead['lead_value'];

                $leads[$key]['lead_value'] = core()->formatBasePrice($lead['lead_value']);
            }

            $totalCount = array_map(function ($count) {
                return core()->formatBasePrice($count);
            }, $totalCount);

            return response()->json([
                'blocks'      => $leads,
                'stage_names' => $pipeline->stages->pluck('name'),
                'stages'      => $pipeline->stages->toArray(),
                'total_count' => $totalCount,
            ]);
        }
    }

    public function getActivities($leadId)
    {
        $activities = app('\App\Repositories\Lead\LeadRepository')->getAllActivities($leadId);
        return response()->json([
            'activities' => $activities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Lead\LeadForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(LeadForm $request)
    {

        $data = request()->all();
        Log::info("..create...\n" . json_encode($data));
        $data['status'] = 1;

        if ($data['lead_pipeline_stage_id']) {
            $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);
            $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;
        }

        if (in_array($stage->code, ['won', 'lost'])) {
            $data['closed_at'] = Carbon::now();
        }
        $lead = $this->leadRepository->create($data);

        $emailTemplate = EmailTemplate::where('name', 'lead created')->first();

        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($lead->person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($lead, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($lead, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {}
        }

        session()->flash('success', trans('app.leads.create-success'));

        return Inertia::location(route('dashboards.leads.index'));
    }

    /**
     * Display a resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        abort_if(Gate::denies('lead_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead = $this->leadRepository->findOrFail($id);

        $currentUser = auth()->user();

        return view('leads.view', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\LeadForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LeadForm $request, $id)
    {
        $data = request()->all();
        Log::info(json_encode($data));

        if (isset($data['lead_pipeline_stage_id'])) {
            $stage = $this->stageRepository->findOrFail($data['lead_pipeline_stage_id']);

            $data['lead_pipeline_id'] = $stage->lead_pipeline_id;
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;
        }
        $lead = $this->leadRepository->update($data, $id);

        $emailTemplate = EmailTemplate::where('name', 'lead updated')->first();

        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($lead->person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($lead, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($lead, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {}
        }

        $prelead = $this->leadRepository->findOrFail($id);
        // $preleadstage = $this->stageRepository->findOrFail($prelead->lead_pipeline_stage_id);
       
        if($stage->code == 'won'){
            $person = $this->personRepository->findOrFail($prelead->person_id);
                
            $filter = [
                'email'           => $person->emails,
                'mobile'          => $person->contact_numbers,
                'organization_id' => $person->organization_id,
            ];

            $exitMember = $this->memberRepository->findByAttributes([
                'email'            => json_encode($filter['email']),
                'mobile'           => json_encode($filter['mobile']),
                'organization_id'  => $filter['organization_id'],
            ])->count();
           
            if($exitMember == 0){
                $member = $this->memberRepository->create($prelead->person_id);

                $emailTemplate = EmailTemplate::where('name', 'member created')->first();

                if ($emailTemplate) {
                    try {
                        Mail::queue(new Common([
                            'to'      => data_get($person->email, '*.value'),
                            'subject' => $this->entity->replacePlaceholders($person, $emailTemplate->subject),
                            'body'    => $this->entity->replacePlaceholders($person, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}
                }
            }
        }
        // else{
        //     if($preleadstage->code == 'won')
        //         $this->memberRepository->delete($id);
        // }

        if (request()->ajax()) {
            return response()->json([
                'message' => trans('app.leads.update-success'),
            ]);
        } else {
            session()->flash('success', trans('app.leads.update-success'));

            if (request()->has('closed_at')) {
                return redirect()->back();
            } else {
                return redirect()->route('leads.index', $data['lead_pipeline_id']);
            }
        }
    }

    /**
     * Search person results.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->leadRepository->findWhere([
            ['title', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /*
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('lead_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead = $this->leadRepository->findOrFail($id);

        try {
            $this->leadRepository->delete($id);

            $emailTemplate = EmailTemplate::where('name', 'lead deleted')->first();

            if ($emailTemplate) {
                try {
                    Mail::queue(new Common([
                        'to'      => data_get($lead->person->emails, '*.value'),
                        'subject' => $this->entity->replacePlaceholders($lead, $emailTemplate->subject),
                        'body'    => $this->entity->replacePlaceholders($lead, $emailTemplate->content),
                    ]));
                } catch (\Exception $e) {}
            }

            return response()->json([
                'message' => trans('app.response.destroy-success', ['name' => trans('app.leads.lead')]),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('app.response.destroy-failed', ['name' => trans('app.leads.lead')]),
            ], 400);
        }
    }

    /**
     * Mass Update the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $data = request()->all();

        foreach ($data['rows'] as $leadId) {
            $lead = $this->leadRepository->find($leadId);

            Event::dispatch('lead.update.before', $leadId);

            $lead->update(['lead_pipeline_stage_id' => $data['value']]);

            Event::dispatch('lead.update.before', $leadId);
        }

        return response()->json([
            'message' => trans('app.response.update-success', ['name' => trans('app.leads.title')])
        ]);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $leadId) {
            Event::dispatch('lead.delete.before', $leadId);

            $this->leadRepository->delete($leadId);

            Event::dispatch('lead.delete.after', $leadId);
        }

        return response()->json([
            'message' => trans('app.response.destroy-success', ['name' => trans('app.leads.title')]),
        ]);
    }
}
