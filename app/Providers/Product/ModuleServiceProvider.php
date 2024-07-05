<?php

namespace App\Providers\Product;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Product\Models\Product::class,
    ];
}
