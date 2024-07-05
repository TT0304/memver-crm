<?php

namespace App\Http\Controllers;

use App\Repositories\Contact\PersonRepository;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Http\Requests\Attribute\AttributeForm;
use Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\Common;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Helpers\Workflow\Entity\Person;
use App\DataStructure\EmailTemplate\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mails\Email;

class PersonController extends Controller
{
    /**
     * Person repository instance.
     *
     * @var \App\Repositories\Contact\PersonRepository
     */
    protected $personRepository;
    protected $emailTemplateRepository;
    protected $entity;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Contact\PersonRepository  $personRepository
     *
     * @return void
     */

    public function __construct(
        PersonRepository $personRepository, 
        EmailTemplateRepository $emailTemplateRepository, 
        Person $entity
    )
    {
        $this->personRepository = $personRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->entity = $entity;
        $this->entity = $entity;

        request()->request->add(['entity_type' => 'persons']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('person_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (request()->ajax()) {
            return app(\App\DataGrids\Contact\PersonDataGrid::class)->toJson();
        }
        return Inertia::render('dashboards/contacts/persons/index', [
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('person_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'persons',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/contacts/persons/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $person = $this->personRepository->create($this->sanitizeRequestedPersonData());

        $emailTemplate = EmailTemplate::where('name', 'person created')->first();
        
        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($person, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($person, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
        }

        session()->flash('success', trans('app.contacts.persons.create-success'));

        return Inertia::location(route('contacts.persons.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('person_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $person = $this->personRepository->findOrFail($id);
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'persons',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('dashboards/contacts/persons/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
            'person' => [
                'id' => $person->id,
                'name' => $person->name,
                'emails' => $person->emails,
                'contact_numbers' => $person->contact_numbers,
                'organization_id' => $person->organization_id,
                'created_at' => $person->created_at,
                'updated_at' => $person->updated_at,
            ]
        ]);

        // return view('contacts.persons.create', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Request\Attribute\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $person = $this->personRepository->update($this->sanitizeRequestedPersonData(), $id);

        $emailTemplate = EmailTemplate::where('name', 'person updated')->first();

        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($person, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($person, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {}
        }

        session()->flash('success', trans('app.contacts.persons.update-success'));

        return Inertia::location(route('contacts.persons.index'));
    }

    /**
     * Search person results.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->personRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);

        return response()->json($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('person_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $person = $this->personRepository->findOrFail($id);

        try {
            $this->personRepository->delete($id);

            $emailTemplate = EmailTemplate::where('name', 'person deleted')->first();

            if ($emailTemplate) {
                try {
                    Mail::queue(new Common([
                        'to'      => data_get($person->emails, '*.value'),
                        'subject' => $this->entity->replacePlaceholders($person, $emailTemplate->subject),
                        'body'    => $this->entity->replacePlaceholders($person, $emailTemplate->content),
                    ]));
                } catch (\Exception $e) {}
            }

            return response()->json([
                'message' => trans('app.response.destroy-success', ['name' => trans('app.contacts.persons.person')]),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('app.response.destroy-failed', ['name' => trans('app.contacts.persons.person')]),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $personId) {
            Event::dispatch('contact.person.delete.before', $personId);

            $this->personRepository->delete($personId);

            Event::dispatch('contact.person.delete.after', $personId);
        }

        return response()->json([
            'message' => trans('app.response.destroy-success', ['name' => trans('app.contacts.persons.title')])
        ]);
    }

    /**
     * Sanitize requested person data and return the clean array.
     *
     * @return array
     */
    private function sanitizeRequestedPersonData(): array
    {
        $data = request()->all();

        $data['contact_numbers'] = collect($data['contact_numbers'])->filter(function ($number) {
            return ! is_null($number['value']);
        })->toArray();

        return $data;
    }
}
