<?php

namespace App\Providers\Contact;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Contact\Models\Person::class,
        \App\DataStructure\Contact\Models\Organization::class,
    ];
}
