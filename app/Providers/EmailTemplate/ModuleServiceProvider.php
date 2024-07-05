<?php

namespace App\Providers\EmailTemplate;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\EmailTemplate\Models\EmailTemplate::class,
    ];
}