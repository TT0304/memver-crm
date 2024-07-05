<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use App\Repositories\Lead\LeadRepository;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class LeadTagController extends Controller
{
    /**
     * LeadRepository object
     *
     * @var \App\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Lead\Repositories\LeadRepository  $leadRepository
     *
     * @return void
     */
    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        Event::dispatch('leads.tag.create.before', $id);

        $lead = $this->leadRepository->find($id);

        if (! $lead->tags->contains(request('id'))) {
            $lead->tags()->attach(request('id'));
        }

        Event::dispatch('leads.tag.create.after', $lead);
        
        return response()->json([
            'status'  => true,
            'message' => trans('app.leads.tag-create-success'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $leadId
     * @param  integer  $tagId
     * @return \Illuminate\Http\Response
     */
    public function delete($leadId)
    {
        abort_if(Gate::denies('leadTag_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Event::dispatch('leads.tag.delete.before', $leadId);

        $lead = $this->leadRepository->find($leadId);

        $lead->tags()->detach(request('tag_id'));

        Event::dispatch('leads.tag.delete.after', $lead);
        
        return response()->json([
            'status'  => true,
            'message' => trans('app.leads.tag-destroy-success'),
        ], 200);
    }
}