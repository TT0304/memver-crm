<?php

namespace App\Providers\Quote;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Quote\Models\Quote::class,
        \App\DataStructure\Quote\Models\QuoteItem::class,
    ];
}
