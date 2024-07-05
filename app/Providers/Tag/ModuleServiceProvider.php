<?php

namespace App\Providers\Tag;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Tag\Models\Tag::class,
    ];
}
