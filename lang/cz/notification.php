<?php

return [
    'groups' => [
        'new' => [
            'mail' => [
                'title' => 'Byl(a) jste přidán(a) do skupiny :name',
                'body' => ':user vás přidal(a) do :name dne :date',
            ],
            'db' => 'Přidán do skupiny :name uživatelem :user',
        ],
    ],

    'event' => [
        'new' => [
            'mail' => [
                'title' => 'Nová událost: :name',
                'action' => 'Zobrazit událost',
                'body' => ':user vytvořil(a) událost :name dne :date',
            ],
            'db' => 'Nová událost :name od :user',
        ],

        'updated' => [
            'mail' => [
                'title' => 'Událost aktualizována: :name',
                'action' => 'Zobrazit událost',
                'body' => ':user aktualizoval(a) :name dne :date',
            ],
            'db' => 'Událost :name aktualizoval :user',
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

