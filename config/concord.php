<?php

return [
    'modules' => [
        \App\Providers\Activity\ModuleServiceProvider::class,
         // \App\Providers\Admin\ModuleServiceProvider::class,
         \App\Providers\Attribute\ModuleServiceProvider::class,
         \App\Providers\Contact\ModuleServiceProvider::class,
         \App\Providers\Core\ModuleServiceProvider::class,
         \App\Providers\Email\ModuleServiceProvider::class,
         \App\Providers\EmailTemplate\ModuleServiceProvider::class,
         \App\Providers\Lead\ModuleServiceProvider::class,
         \App\Providers\Product\ModuleServiceProvider::class,
         \App\Providers\Quote\ModuleServiceProvider::class,
         \App\Providers\Tag\ModuleServiceProvider::class,
         // \App\UI\Providers\ModuleServiceProvider::class,
         \App\Providers\User\ModuleServiceProvider::class,
         \App\Providers\Workflow\ModuleServiceProvider::class,
    ],
    'register_route_models' => true
];
