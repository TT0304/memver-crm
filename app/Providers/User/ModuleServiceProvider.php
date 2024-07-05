<?php

namespace App\Providers\User;

use App\Providers\Core\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \App\DataStructure\User\Models\Group::class,
        \App\DataStructure\User\Models\Permission::class,
        \App\DataStructure\User\Models\Role::class,
        \App\DataStructure\User\Models\User::class,
    ];
}
