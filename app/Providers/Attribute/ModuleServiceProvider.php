<?php

namespace App\Providers\Attribute;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Attribute\Models\Attribute::class,
        \App\DataStructure\Attribute\Models\AttributeOption::class,
        \App\DataStructure\Attribute\Models\AttributeValue::class,
    ];
}
