<?php

namespace App\Repositories\Workflow;

use App\Core\Eloquent\Repository;

class WorkflowRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\DataStructure\Workflow\Contracts\Workflow';
    }
}