<?php

$prefix = 'nawasara-docs';

return [
    [
        'label' => 'Components',
        'icon' => 'heroicon-o-cube',
        'url' => '',
        'permission' => 'nawasara-core.component.view',
        'submenu' => [
            [
                'label' => 'Table',
                'icon' => 'heroicon-o-puzzle-piece',
                'url' => url($prefix.'/components/table'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
            [
                'label' => 'Base Komponen',
                'icon' => 'heroicon-o-puzzle-piece',
                'url' => url($prefix.'/components/base'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
            [
                'label' => 'Form Komponen',
                'icon' => 'heroicon-o-puzzle-piece',
                'url' => url($prefix.'/components/form'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
        ],
    ],
];
