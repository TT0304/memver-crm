<?php

namespace App\Helpers\Workflow;

use App\Repositories\Attribute\AttributeRepository;
use App\Repositories\EmailTemplate\EmailTemplateRepository;

class Entity
{
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
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Attribute\AttributeRepository  $attributeRepository
     * @param  \App\Repositories\EmailTemplate\EmailTemplateRepository  $emailTemplateRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        EmailTemplateRepository $emailTemplateRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    /**
     * Returns events to match for the entity
     * 
     * @return array
     */
    public function getEvents()
    {
        $entities = config('workflows.trigger_entities');

        $events = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $events[$key] = [
                'id'     => $key,
                'name'   => $entity['name'],
                'events' => $entity['events'],
            ];
        }

        return $events;
    }

    /**
     * Returns conditions to match for the entity
     * 
     * @return array
     */
    public function getConditions()
    {
        $entities = config('workflows.trigger_entities');

        $conditions = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $conditions[$key] = $object->getConditions();
        }

        return $conditions;
    }

    /**
     * Returns workflow actions
     * 
     * @return array
     */
    public function getActions()
    {
        $entities = config('workflows.trigger_entities');

        $conditions = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $conditions[$key] = $object->getActions();
        }

        return $conditions;
    }

    /**
     * Returns placeholders for email templates
     * 
     * @return array
     */
    public function getEmailTemplatePlaceholders()
    {
        $entities = config('workflows.trigger_entities');

        $placeholders = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $placeholders[] = $object->getEmailTemplatePlaceholders($entity);
        }

        return $placeholders;
    }
}