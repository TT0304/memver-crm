<?php

namespace App\Helpers\Workflow\Entity;

use Illuminate\Support\Facades\Mail;
use App\Notifications\Common;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Attribute\AttributeRepository;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Contact\PersonRepository;
use App\Repositories\Activity\ActivityRepository;


class Lead extends AbstractEntity
{
    /**
     * @var string  $code
     */
    protected $entityType = 'leads';

    /**
     * AttributeRepository object
     *
     * @var \App\Repositories\Attribute\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * EmailTemplateRepository object
     *
     * @var \App\Repositories\EmailTemplate\EmailTemplateRepository
     */
    protected $emailTemplateRepository;

    /**
     * LeadRepository object
     *
     * @var \App\Repositories\Lead\LeadRepository
     */
    protected $leadRepository;

    /**
     * ActivityRepository object
     *
     * @var \App\Repositories\Activity\ActivityRepository
     */
    protected $activityRepository;

    /**
     * PersonRepository object
     *
     * @var \App\Repositories\Contact\PersonRepository
     */
    protected $personRepository;

    /**
     * TagRepository object
     *
     * @var \App\Repositories\Tag\TagRepository
     */
    protected $tagRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Attribute\AttributeRepository  $attributeRepository
     * @param  \App\Repositories\EmailTemplate\EmailTemplateRepository  $emailTemplateRepository
     * @param  \App\Repositories\Lead\LeadRepository  $leadRepository
     * @param \App\Repositories\Activity\ActivityRepository  $activityRepository
     * @param \App\Repositories\Contact\PersonRepository  $personRepository
     * @param  \App\Repositories\Tag\TagRepository  $tagRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        EmailTemplateRepository $emailTemplateRepository,
        LeadRepository $leadRepository,
        ActivityRepository $activityRepository,
        PersonRepository $personRepository,
        TagRepository $tagRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->leadRepository = $leadRepository;

        $this->activityRepository = $activityRepository;

        $this->personRepository = $personRepository;

        $this->tagRepository = $tagRepository;
    }

    /**
     * Returns entity
     * 
     * @param  \App\DataStructure\Lead\Contracts\Lead|integer  $entity
     * @return \App\DataStructure\Lead\Contracts\Lead
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \App\DataStructure\Lead\Contracts\Lead) {
            $entity = $this->leadRepository->find($entity);
        }

        return $entity;
    }

    /**
     * Returns attributes
     *
     * @param  string  $entityType
     * @param  array  $skipAttributes
     * @return array
     */
    public function getAttributes($entityType, $skipAttributes = ['textarea', 'image', 'file', 'address'])
    {
        $attributes[] = [
            'id'          => 'lead_pipeline_stage_id',
            'type'        => 'select',
            'name'        => 'Stage',
            'lookup_type' => 'lead_pipeline_stages',
            'options'     => collect([]),
        ];

        return array_merge(
            parent::getAttributes($entityType, $skipAttributes),
            $attributes
        );
    }

    /**
     * Returns workflow actions
     * 
     * @return array
     */
    public function getActions()
    {
        $emailTemplates = $this->emailTemplateRepository->all(['id', 'name']);

        return [
            [
                'id'         => 'update_lead',
                'name'       => __('admin::app.settings.workflows.update-lead'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'         => 'update_person',
                'name'       => __('admin::app.settings.workflows.update-person'),
                'attributes' => $this->getAttributes('persons'),
            ], [
                'id'      => 'send_email_to_person',
                'name'    => __('admin::app.settings.workflows.send-email-to-person'),
                'options' => $emailTemplates,
            ], [
                'id'      => 'send_email_to_sales_owner',
                'name'    => __('admin::app.settings.workflows.send-email-to-sales-owner'),
                'options' => $emailTemplates,
            ], [
                'id'   => 'add_tag',
                'name' => __('admin::app.settings.workflows.add-tag'),
            ], [
                'id'   => 'add_note_as_activity',
                'name' => __('admin::app.settings.workflows.add-note-as-activity'),
            ],
        ];
    }

    /**
     * Execute workflow actions
     * 
     * @param  \App\DataStructure\Workflow\Contracts\Workflow  $workflow
     * @param  \App\DataStructure\Lead\Contracts\Lead  $lead
     * @return array
     */
    public function executeActions($workflow, $lead)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_lead':
                    $this->leadRepository->update([
                        'entity_type'        => 'leads',
                        $action['attribute'] => $action['value'],
                    ], $lead->id);

                    break;

                case 'update_person':
                    $this->personRepository->update([
                        'entity_type'        => 'persons',
                        $action['attribute'] => $action['value'],
                    ], $lead->person_id);

                    break;
                    
                case 'send_email_to_person':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => data_get($lead->person->emails, '*.value'),
                            'subject' => $this->replacePlaceholders($lead, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($lead, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;
                    
                case 'send_email_to_sales_owner':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => $lead->user->email,
                            'subject' => $this->replacePlaceholders($lead, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($lead, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;
            
                case 'add_tag':
                    $colors = [
                        '#337CFF',
                        '#FEBF00',
                        '#E5549F',
                        '#27B6BB',
                        '#FB8A3F',
                        '#43AF52'
                    ];

                    if (! $tag = $this->tagRepository->findOneByField('name', $action['value'])) {
                        $tag = $this->tagRepository->create([
                            'name'    => $action['value'],
                            'color'   => $colors[rand(0, 5)],
                            'user_id' => auth()->guard('client_users')->user()->id,
                        ]);
                    }

                    if (! $lead->tags->contains($tag->id)) {
                        $lead->tags()->attach($tag->id);
                    }

                    break;
            
                case 'add_note_as_activity':
                    $activity = $this->activityRepository->create([
                        'type'    => 'note',
                        'comment' => $action['value'],
                        'is_done' => 1,
                        'user_id' => $userId = auth()->guard('client_users')->user()->id,
                    ]);

                    $lead->activities()->attach($activity->id);
                    
                    break;
            }    
        }
    }
}