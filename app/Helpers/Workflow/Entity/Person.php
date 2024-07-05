<?php

namespace App\Helpers\Workflow\Entity;

use Illuminate\Support\Facades\Mail;
use App\Notifications\Common;
use App\Repositories\Attribute\AttributeRepository;
use App\Repositories\EmailTemplate\EmailTemplateRepository;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Contact\PersonRepository;

class Person extends AbstractEntity
{
    /**
     * @var string  $code
     */
    protected $entityType = 'persons';

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
     * PersonRepository object
     *
     * @var \App\Repositories\Contact\PersonRepository
     */
    protected $personRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Attribute\AttributeRepository  $attributeRepository
     * @param  \App\Repositories\EmailTemplate\EmailTemplateRepository  $emailTemplateRepository
     * @param  \App\Repositories\Lead\LeadRepository  $leadRepository
     * @param \App\Repositories\Contact\PersonRepository  $personRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        EmailTemplateRepository $emailTemplateRepository,
        LeadRepository $leadRepository,
        PersonRepository $personRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->leadRepository = $leadRepository;

        $this->personRepository = $personRepository;
    }

    /**
     * Returns entity
     * 
     * @param  \App\DataStructure\Contact\Contracts\Person|integer  $entity
     * @return \App\DataStructure\Contact\Contracts\Person
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \App\DataStructure\Contact\Contracts\Person) {
            $entity = $this->personRepository->find($entity);
        }

        return $entity;
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
                'id'         => 'update_person',
                'name'       => __('admin::app.settings.workflows.update-person'),
                'attributes' => $this->getAttributes('persons'),
            ], [
                'id'         => 'update_related_leads',
                'name'       => __('admin::app.settings.workflows.update-related-leads'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'      => 'send_email_to_person',
                'name'    => __('admin::app.settings.workflows.send-email-to-person'),
                'options' => $emailTemplates,
            ],
        ];
    }

    /**
     * Execute workflow actions
     * 
     * @param  \App\DataStructure\Workflow\Contracts\Workflow  $workflow
     * @param  \App\DataStructure\Contact\Contracts\Person  $person
     * @return array
     */
    public function executeActions($workflow, $person)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_person':
                    $this->personRepository->update([
                        'entity_type'        => 'persons',
                        $action['attribute'] => $action['value'],
                    ], $person->id);

                    break;

                case 'update_related_leads':
                    $leads = $this->leadRepository->findByField('person_id', $person->id);

                    foreach ($leads as $lead) {
                        $this->leadRepository->update([
                            'entity_type'        => 'leads',
                            $action['attribute'] => $action['value'],
                        ], $lead->id);
                    }

                    break;

                case 'send_email_to_person':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => data_get($person->emails, '*.value'),
                            'subject' => $this->replacePlaceholders($person, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($person, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;
            }
        }
    }
}