<?php

namespace App\Workflow\Listeners;

use App\Repositories\Workflow\WorkflowRepository;
use App\Helpers\Workflow\Validator;

class Entity
{
    /**
     * WorkflowRepository object
     *
     * @var \App\Repositories\Workflow\WorkflowRepository
     */
    protected $workflowRepository;

    /**
     * Validator object
     *
     * @var \App\Helpers\Workflow\Validator
     */
    protected $validator;

    /**
     * Create a new repository instance.
     *
     * @param  \App\Repositories\Workflow\WorkflowRepository  $workflowRepository
     * @param  \App\Helpers\Workflow\Validator  $validator
     * @return void
     */
    public function __construct(
        WorkflowRepository $workflowRepository,
        Validator $validator
    )
    {
        $this->workflowRepository = $workflowRepository;

        $this->validator = $validator;
    }

    /**
     * @param  string  $eventName
     * @param  mixed  $entity
     * @return void
     */
    public function process($eventName, $entity)
    {
        $workflows = $this->workflowRepository->findByField('event', $eventName);

        foreach ($workflows as $workflow) {
            $workflowEntity = app(config('workflows.trigger_entities.' . $workflow->entity_type . '.class'));

            $entity = $workflowEntity->getEntity($entity);

            if (! $this->validator->validate($workflow, $entity)) {
                continue;
            }

            try {
                $workflowEntity->executeActions($workflow, $entity);
            } catch (\Exception $e) {
                // Skip exception for now
            }
        }
    }
}