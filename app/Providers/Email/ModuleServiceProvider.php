<?php

namespace App\Providers\Email;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Email\Models\Email::class,
        \App\DataStructure\Email\Models\Attachment::class,
    ];
}
