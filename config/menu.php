<?php

$prefix = 'nawasara-docs';

return [
    [
        'label' => 'Components',
        'icon' => 'lucide-boxes',
        'url' => '',
        'permission' => 'nawasara-core.component.view',
        'submenu' => [
            [
                'label' => 'Table',
                'icon' => 'lucide-table-2',
                'url' => url($prefix.'/components/table'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
            [
                'label' => 'Base Komponen',
                'icon' => 'lucide-puzzle',
                'url' => url($prefix.'/components/base'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
            [
                'label' => 'Form Komponen',
                'icon' => 'lucide-form-input',
                'url' => url($prefix.'/components/form'),
                'permission' => 'nawasara-core.component.view',
                'navigate' => true,
            ],
        ],
    ],
];
