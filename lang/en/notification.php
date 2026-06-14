<?php

return [
    'language' => 'en',
    'ui' => [
        'title' => 'Notifications',
        'read_all' => 'Mark all as read',
        'read' => 'Read',
        'mark_as_read' => 'Mark as read',
        'empty' => 'No notifications found.',
        'load_failed' => 'Could not load notifications. Refresh the page and try again.',
        'single_read_fallback' => 'Notification succesfully marked as read.',
        'read_all_failed' => 'Mark all as read failed. Try again.',
        'unknown_time' => 'Unknown time',
    ],
    
    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'You were added to group :name',
                'body' => ':user added you to :name on :date',
            ],
            'db' => 'Added to group :name',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'New event: :name',
                'action' => 'View event',
                'body' => ':user created :name on :date',
            ],
            'db' => 'New event :name',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Event updated: :name',
                'action' => 'View event',
                'body' => ':user updated :name on :date',
            ],
            'db' => 'Event :name updated',
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

