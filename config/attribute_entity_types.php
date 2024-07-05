<?php

return [
    'leads'         => [
        'name'       => 'Lead',
        'repository' => 'App\Repositories\Lead\LeadRepository',
    ],

    'persons'       => [
        'name'       => 'Person',
        'repository' => 'App\Repositories\Contact\PersonRepository',
    ],

    'organizations' => [
        'name'       => 'Organization',
        'repository' => 'App\Repositories\Contact\OrganizationRepository',
    ],

    'products'      => [
        'name'       => 'Product',
        'repository' => 'App\Repositories\Product\ProductRepository',
    ],

    'quotes'      => [
        'name'       => 'Quote',
        'repository' => 'App\Repositories\Quote\QuoteRepository',
    ],
];