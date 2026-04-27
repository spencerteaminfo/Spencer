<?php

return [
    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'Sie wurden zur Gruppe :name hinzugefügt',
                'body' => ':user hat Sie am :date zur Gruppe :name hinzugefügt',
            ],
            'db' => 'Zur Gruppe :name hinzugefügt von :user',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'Neue Veranstaltung: :name',
                'action' => 'Veranstaltung anzeigen',
                'body' => ':user hat :name am :date erstellt',
            ],
            'db' => 'Neue Veranstaltung :name von :user',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Veranstaltung aktualisiert: :name',
                'action' => 'Veranstaltung anzeigen',
                'body' => ':user hat :name am :date aktualisiert',
            ],
            'db' => 'Veranstaltung :name aktualisiert von :user',
        ],

        'payment' => [
            'paid' => [
                'mail' => [
                    'title' => 'Zahlung für :name erhalten',
                    'action' => 'Veranstaltung anzeigen',
                    'body' => ':user hat für :name am :date bezahlt',
                ],
                'db' => ':user hat für Veranstaltung :name bezahlt',
            ],
        ],
    ],
];

