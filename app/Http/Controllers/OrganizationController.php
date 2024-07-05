<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use App\Http\Requests\Attribute\AttributeForm;
use App\Repositories\Contact\OrganizationRepository;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    /**
     * OrganizationRepository object
     *
     * @var \App\Product\Repositories\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Product\OrganizationRepository  $organizationRepository
     *
     * @return void
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;

        request()->request->add(['entity_type' => 'organizations']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('organization_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if (request()->ajax()) {
            return app(\App\DataGrids\Contact\OrganizationDataGrid::class)->toJson();
        }
        return Inertia::render('dashboards/contacts/organizations/index', [
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('organization_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'organizations',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $groupStates = core()->groupedStatesByCountries();
        $countries = core()->countries();

        return Inertia::render('dashboards/contacts/organizations/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
            'groupStates' => $groupStates,
            'countries' => $countries,
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
        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create(request()->all());

        Event::dispatch('contacts.organization.create.after', $organization);

        session()->flash('success', trans('app.contacts.organizations.create-success'));

        return Inertia::location(route('contacts.organizations.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('organization_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $organization = $this->organizationRepository->findOrFail($id);

        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'organizations',
        ]);

        $currencyCode = core()->currencySymbol(config('app.currency'));
        $groupStates = core()->groupedStatesByCountries();
        $countries = core()->countries();        

        return Inertia::render('dashboards/contacts/organizations/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
            'groupStates' => $groupStates,
            'countries' => $countries,
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                // 'address' => $organization->address,
                'created_at' => $organization->created_at,
                'updated_at' => $organization->updated_at,
            ]
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('contacts.organization.update.before', $id);

        $organization = $this->organizationRepository->update(request()->all(), $id);

        Event::dispatch('contacts.organization.update.after', $organization);

        session()->flash('success', trans('app.contacts.organizations.update-success'));

        return Inertia::location(route('contacts.organizations.index'));

    }
    
    public function search()
    {
        $results = $this->organizationRepository->findWhere([
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
        abort_if(Gate::denies('organization_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->organizationRepository->findOrFail($id);

        try {
            Event::dispatch('contact.organization.delete.before', $id);

            $this->organizationRepository->delete($id);

            Event::dispatch('contact.organization.delete.after', $id);

            return response()->json([
                'message' => trans('app.response.destroy-success', ['name' => trans('app.contacts.organizations.organization')]),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('app.response.destroy-failed', ['name' => trans('app.contacts.organizations.organization')]),
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
        foreach (request('rows') as $organizationId) {
            Event::dispatch('contact.organization.delete.before', $organizationId);

            $this->organizationRepository->delete($organizationId);

            Event::dispatch('contact.organization.delete.after', $organizationId);
        }

        return response()->json([
            'message' => trans('app.response.destroy-success', ['name' => trans('app.contacts.organizations.title')])
        ]);
    }
}
