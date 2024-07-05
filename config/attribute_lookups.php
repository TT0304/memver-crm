<?php

return [
    'leads' => [
        'name'         => 'Leads',
        'repository'   => 'App\Repositories\Lead\LeadRepository',
        'label_column' => 'title',
    ],

    'lead_sources' => [
        'name'         => 'Lead Sources',
        'repository'   => 'App\Repositories\Lead\SourceRepository',
    ],

    'lead_types' => [
        'name'         => 'Lead Types',
        'repository'   => 'App\Repositories\Lead\TypeRepository',
    ],

    'lead_pipelines' => [
        'name'         => 'Lead Pipelines',
        'repository'   => 'App\Repositories\Lead\PipelineRepository',
    ],

    'lead_pipeline_stages' => [
        'name'         => 'Lead Pipeline Stages',
        'repository'   => 'App\Repositories\Lead\StageRepository',
    ],

    'users' => [
        'name'         => 'Sales Owners',
        'repository'   => 'App\Repositories\User\UserRepository',
    ],

    'organizations' => [
        'name'         => 'Organizations',
        'repository'   => 'App\Repositories\Contact\OrganizationRepository',
    ],

    'persons' => [
        'name'         => 'Persons',
        'repository'   => 'App\Repositories\Contact\PersonRepository',
    ],

    'roles' => [
        'name'         => 'Roles',
        'repository'   => 'App\Repositories\User\RoleRepository',
    ]
];