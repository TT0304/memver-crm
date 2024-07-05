<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade as PDF;
use App\DataGrids\Quote\QuoteDataGrid;
use App\Http\Requests\Attribute\AttributeForm;
use App\Repositories\Quote\QuoteRepository;
use App\Repositories\Lead\LeadRepository;
use Illuminate\Support\Facades\Log;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Helpers\Workflow\Entity\Quote;
use App\DataStructure\EmailTemplate\Models\EmailTemplate;

class QuoteController extends Controller
{
    /**
     * QuoteRepository object
     *
     * @var \App\Quote\Repositories\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * LeadRepository object
     *
     * @var \App\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;
    protected $emailTemplateRepository;
    protected $entity;

    /**
     * Create a new controller instance.
     *
     * @param \App\Quote\Repositories\QuoteRepository  $quoteRepository
     * @param \App\Lead\Repositories\LeadRepository  $leadRepository
     *
     * @return void
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LeadRepository $leadRepository, 
        EmailTemplateRepository $emailTemplateRepository,
        Quote $entity
    ) {
        $this->quoteRepository = $quoteRepository;

        $this->leadRepository = $leadRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->entity = $entity;

        request()->request->add(['entity_type' => 'quotes']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index_page()
    {
        abort_if(Gate::denies('quote_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (request()->ajax()) {
            return app(QuoteDataGrid::class)->toJson();
        }
        return Inertia::render('dashboards/quotes/index', []);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('quote_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $lead = $this->leadRepository->find(request('id'));
        $quote = app('\App\Repositories\Quote\QuoteRepository')->getModel();

        if (isset($lead)) {
            $quote->fill([
                'person_id'       => $lead->person_id,
                'user_id'         => $lead->user_id,
                'billing_address' => $lead->person->organization ? $lead->person->organization->address : null
            ]);
        }
        $currencyCode = core()->currencySymbol(config('app.currency'));
        $customAttributes1 = app('App\Repositories\Attribute\AttributeRepository')->scopeQuery(function ($query) {
            return $query
                ->where('entity_type', 'quotes')
                ->whereIn('code', [
                    'user_id',
                    'subject',
                    'description',
                    'expired_at',
                    'person_id',
                ]);
        })->get();
        
        $customAttributes2 = app('App\Repositories\Attribute\AttributeRepository')
            ->scopeQuery(function ($query) {
                return $query
                    ->where('entity_type', 'quotes')
                    ->whereIn('code', [
                        'billing_address',
                        'shipping_address',
                    ]);
            })->get();
        $lookUpEntityData = app('App\Repositories\Attribute\AttributeRepository')
            ->getLookUpEntity('leads', request('id'));
        $groupStates = core()->groupedStatesByCountries();
        $countries = core()->countries();        
        return Inertia::render('dashboards/quotes/create', [
            'lead' => $lead,
            'customAttributes1' => $customAttributes1,
            'customAttributes2' => $customAttributes2,
            'entity' => $quote,
            'currencyCode' => $currencyCode,
            'lookUpEntityData' => $lookUpEntityData,
            'groupStates' => $groupStates,
            'countries' => $countries,
            'customValidations' => [
                'expired_at' => [
                    'required',
                    'date_format:YYYY-MM-DD',
                    'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                ],
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $quote = $this->quoteRepository->create(request()->all());

        $emailTemplate = EmailTemplate::where('name', 'quote created')->first();

        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($quote->person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($quote, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($quote, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {}
        }


        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            $lead->quotes()->attach($quote->id);
        }

        session()->flash('success', trans('admin::app.quotes.create-success'));

        return redirect()->route('quotes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('quote_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $quote = $this->quoteRepository->findOrFail($id);

        return view('admin::quotes.edit', compact('quote'));
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
        $quote = $this->quoteRepository->update(request()->all(), $id);

        $emailTemplate = EmailTemplate::where('name', 'quote updated')->first();

        if ($emailTemplate) {
            try {
                Mail::queue(new Common([
                    'to'      => data_get($quote->person->emails, '*.value'),
                    'subject' => $this->entity->replacePlaceholders($quote, $emailTemplate->subject),
                    'body'    => $this->entity->replacePlaceholders($quote, $emailTemplate->content),
                ]));
            } catch (\Exception $e) {}
        }

        $quote->leads()->detach();

        if (request('lead_id')) {
            $lead = $this->leadRepository->find(request('lead_id'));

            $lead->quotes()->attach($quote->id);
        }

        session()->flash('success', trans('admin::app.quotes.update-success'));

        return redirect()->route('quotes.index');
    }

    /**
     * Search quote results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $results = $this->quoteRepository->findWhere([
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
        abort_if(Gate::denies('quote_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $quote = $this->quoteRepository->findOrFail($id);

        try {
            $this->quoteRepository->delete($id);

            $emailTemplate = EmailTemplate::where('name', 'quote deleted')->first();

            if ($emailTemplate) {
                try {
                    Mail::queue(new Common([
                        'to'      => data_get($quote->person->emails, '*.value'),
                        'subject' => $this->entity->replacePlaceholders($quote, $emailTemplate->subject),
                        'body'    => $this->entity->replacePlaceholders($quote, $emailTemplate->content),
                    ]));
                } catch (\Exception $e) {}
            }
            
            return response()->json([
                'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.quotes.quote')]),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.quotes.quote')]),
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
        abort_if(Gate::denies('quote_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        foreach (request('rows') as $quoteId) {
            Event::dispatch('quote.delete.before', $quoteId);

            $this->quoteRepository->delete($quoteId);

            Event::dispatch('quote.delete.after', $quoteId);
        }

        return response()->json([
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.quotes.title')]),
        ]);
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        abort_if(Gate::denies('quote_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $quote = $this->quoteRepository->findOrFail($id);

        return PDF::loadHTML(view('admin::quotes.pdf', compact('quote'))->render())
            ->setPaper('a4')
            ->download('Quote_' . $quote->subject . '.pdf');
    }
}
