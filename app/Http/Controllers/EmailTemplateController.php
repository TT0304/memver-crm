<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Event;
use App\Http\Controllers\Controller;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Helpers\Workflow\Entity;
use Inertia\Inertia;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class EmailTemplateController extends Controller
{
    /**
     * EmailTemplateRepository object
     *
     * @var App\Repositories\EmailTemplate\EmailTemplateRepository
     */
    protected $emailTemplateRepository;

    /**
     * Entity object
     *
     * @var \App\Helpers\Workflow\Entity
     */
    protected $workflowEntityHelper;

    // App\Helpers\Workflow\Entity\AbstractEntity


    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\EmailTemplate\EmailTemplateRepository  $emailTemplateRepository
     * @param  \App\Helpers\Workflow\Entity  $workflowEntityHelper
     * @return void
     */
    public function __construct(
        EmailTemplateRepository $emailTemplateRepository,
        Entity $workflowEntityHelper,
    )
    {
        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->workflowEntityHelper = $workflowEntityHelper;

        request()->request->add(['entity_type' => 'email_templates']);

    }

    /**
     * Display a listing of the email template.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('email_template_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (request()->ajax()) {
            return app(\App\DataGrids\EmailTemplate\EmailTemplateDataGrid::class)->toJson();
        }
        return Inertia::render('settings/email_templates/index', []);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(Gate::denies('email_template_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'email_templates',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('settings/email_templates/create', [
            'customAttributes' => $customAttributes,
            'currencyCode' => $currencyCode,
            'placeholders' => $placeholders
        ]);
    }

    /**
     * Store a newly created email templates in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.create.before');

        $emailTemplate = $this->emailTemplateRepository->create(request()->all());

        Event::dispatch('settings.email_templates.create.after', $emailTemplate);

        session()->flash('success', trans('app.settings.email-templates.create-success'));

        return Inertia::location(route('email_templates.index', []));
    }

    /**
     * Show the form for editing the specified email template.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        abort_if(Gate::denies('email_template_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();

        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();
        $customAttributes = app('App\Repositories\Attribute\AttributeRepository')->findWhere([
            'entity_type' => 'email_templates',
        ]);
        $currencyCode = core()->currencySymbol(config('app.currency'));
        return Inertia::render('settings/email_templates/create', [
            'customAttributes' => $customAttributes,
            'currencyCode'     => $currencyCode,
            'placeholders'     => $placeholders,
            'emailTemplate'    => $emailTemplate
        ]);
    }

    /**
     * Update the specified email template in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.update.before', $id);

        $emailTemplate = $this->emailTemplateRepository->update(request()->all(), $id);

        Event::dispatch('settings.email_templates.update.after', $emailTemplate);

        session()->flash('success', trans('app.settings.email-templates.update-success'));

        return Inertia::location(route('email_templates.index', []));
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('email_template_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        try {
            Event::dispatch('settings.email_templates.delete.before', $id);

            $this->emailTemplateRepository->delete($id);

            Event::dispatch('settings.email_templates.delete.after', $id);

            return response()->json([
                'message' => trans('app.settings.email-templates.delete-success'),
            ], 200);
        } catch(\Exception $exception) {
            return response()->json([
                'message' => trans('app.settings.email-templates.delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('app.settings.email-templates.delete-failed'),
        ], 400);
    }
}
