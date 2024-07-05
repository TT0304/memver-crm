<?php

return [
    'trigger_entities' => [

        'leads' => [
            'name'   => 'Leads',
            'class'  => 'App\Helpers\Workflow\Entity\Lead',
            'events' => [
                [
                    'event' => 'lead.create.after',
                    'name'  => 'Created',  
                ], [
                    'event' => 'lead.update.after',
                    'name'  => 'Updated',  
                ], [
                    'event' => 'lead.delete.before',
                    'name'  => 'Deleted',  
                ],
            ]
        ],

        // 'activities' => [
        //     'name'   => 'Activities',
        //     'class'  => 'App\Helpers\Workflow\Entity\Activity',
        //     'events' => [
        //         [
        //             'event' => 'activity.create.after',
        //             'name'  => 'Created',  
        //         ], [
        //             'event' => 'activity.update.after',
        //             'name'  => 'Updated',  
        //         ], [
        //             'event' => 'activity.delete.before',
        //             'name'  => 'Deleted',  
        //         ],
        //     ]
        // ],
        
        'persons' => [
            'name'   => 'Persons',
            'class'  => 'App\Helpers\Workflow\Entity\Person',
            'events' => [
                [
                    'event' => 'contacts.person.create.after',
                    'name'  => 'Created',  
                ], [
                    'event' => 'contacts.person.update.after',
                    'name'  => 'Updated',  
                ], [
                    'event' => 'contacts.person.delete.before',
                    'name'  => 'Deleted',  
                ],
            ]
        ],

        'quotes' => [
            'name'   => 'Quotes',
            'class'  => 'App\Helpers\Workflow\Entity\Quote',
            'events' => [
                [
                    'event' => 'quote.create.after',
                    'name'  => 'Created',  
                ], [
                    'event' => 'quote.update.after',
                    'name'  => 'Updated',  
                ], [
                    'event' => 'quote.delete.before',
                    'name'  => 'Deleted',  
                ],
            ]
        ]
    ]
];