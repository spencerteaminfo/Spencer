<?php

return [
    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'You were added to group :name',
                'body' => ':user added you to :name on :date',
            ],
            'db' => 'Added to group :name by :user',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'New event: :name',
                'action' => 'View event',
                'body' => ':user created :name on :date',
            ],
            'db' => 'New event :name by :user',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Event updated: :name',
                'action' => 'View event',
                'body' => ':user updated :name on :date',
            ],
            'db' => 'Event :name updated by :user',
        ],

        'payment' => [
            'paid' => [
                'mail' => [
                    'title' => 'Payment received for :name',
                    'action' => 'View event',
                    'body' => ':user paid for :name on :date',
                ],
                'db' => ':user paid for event :name',
            ],
        ],
    ],
];

