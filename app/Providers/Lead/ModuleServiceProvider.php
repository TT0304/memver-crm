<?php

namespace App\Providers\Lead;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Lead\Models\Lead::class,
        \App\DataStructure\Lead\Models\Pipeline::class,
        \App\DataStructure\Lead\Models\Product::class,
        \App\DataStructure\Lead\Models\Source::class,
        \App\DataStructure\Lead\Models\Stage::class,
        \App\DataStructure\Lead\Models\Type::class,
        \App\DataStructure\Lead\Models\Member::class,
    ];
}
