<?php

namespace App\Providers\Core;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Core\Models\CoreConfig::class,
        \App\DataStructure\Core\Models\Country::class,
        \App\DataStructure\Core\Models\CountryState::class,
    ];
}
