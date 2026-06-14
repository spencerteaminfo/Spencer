<?php

return [
    'language' => 'cz',
    'ui' => [
        'title' => 'Oznámení',
        'read_all' => 'Označit vše jako přečtené',
        'read' => 'Přečteno',
        'mark_as_read' => 'Označit jako přečtené',
        'empty' => 'Žádná oznámení nenalezena.',
        'load_failed' => 'Oznámení se nepodařilo načíst. Obnovte stránku a zkuste to znovu.',
        'single_read_fallback' => 'Oznámení úspěšně označeno jako přečtené.',
        'read_all_failed' => 'Označení všech jako přečtených selhalo. Zkuste to znovu.',
        'unknown_time' => 'Neznámý čas',
    ],

    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'Byl(a) jste přidán(a) do skupiny :name',
                'body' => ':user vás přidal(a) do :name dne :date',
            ],
            'db' => 'Přidán do skupiny :name',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'Nová událost: :name',
                'action' => 'Zobrazit událost',
                'body' => ':user vytvořil(a) událost :name dne :date',
            ],
            'db' => 'Nová událost :name',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Událost aktualizována: :name',
                'action' => 'Zobrazit událost',
                'body' => ':user aktualizoval(a) :name dne :date',
            ],
            'db' => 'Událost :name aktualizována',
        ],

        'payment' => [
            'paid' => [
                'mail' => [
                    'title' => 'Platba přijata za :name',
                    'action' => 'Zobrazit událost',
                    'body' => ':user zaplatil(a) za :name dne :date',
                ],
                'db' => ':user zaplatil(a) za událost :name',
            ],
        ],
    ],
];

