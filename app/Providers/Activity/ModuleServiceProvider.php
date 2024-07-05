<?php

namespace App\Providers\Activity;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\Activity\Models\Activity::class,
        \App\DataStructure\Activity\Models\File::class,
        \App\DataStructure\Activity\Models\Participant::class,
    ];
}
