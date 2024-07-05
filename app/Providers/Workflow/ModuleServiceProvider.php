<?php

namespace App\Providers\Workflow;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Workflow\Models\Workflow::class,
    ];
}