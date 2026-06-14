<?php

return [
    'language' => 'de',
    'ui' => [
        'title' => 'Benachrichtigungen',
        'read_all' => 'Alle als gelesen markieren',
        'read' => 'Gelesen',
        'mark_as_read' => 'Als gelesen markieren',
        'empty' => 'Keine Benachrichtigungen gefunden.',
        'load_failed' => 'Benachrichtigungen konnten nicht geladen werden. Aktualisieren Sie die Seite und versuchen Sie es erneut.',
        'single_read_fallback' => 'Benachrichtigung erfolgreich als gelesen markiert.',
        'read_all_failed' => 'Alle als gelesen markieren ist fehlgeschlagen. Bitte erneut versuchen.',
        'unknown_time' => 'Unbekannte Zeit',
    ],
    
    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'Sie wurden zur Gruppe :name hinzugefügt',
                'body' => ':user hat Sie am :date zur Gruppe :name hinzugefügt',
            ],
            'db' => 'Zur Gruppe :name',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'Neue Veranstaltung: :name',
                'action' => 'Veranstaltung anzeigen',
                'body' => ':user hat :name am :date erstellt',
            ],
            'db' => 'Neue Veranstaltung :name',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Veranstaltung aktualisiert: :name',
                'action' => 'Veranstaltung anzeigen',
                'body' => ':user hat :name am :date aktualisiert',
            ],
            'db' => 'Veranstaltung :name aktualisiert',
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

