<?php

/*
| Dokumentasi tampil di sidebar supaya bisa ditemukan tanpa harus tahu
| URL-nya. Sebelumnya sengaja disembunyikan, tapi dokumentasi yang hanya
| bisa dibuka oleh yang sudah hafal alamatnya tidak berguna bagi orang yang
| paling membutuhkannya — yang baru bergabung.
|
| Tanpa `permission`: isinya cara memakai komponen dan API, bukan data.
| WorkspaceManager memperlakukan permission null sebagai "boleh untuk semua
| yang sudah login", dan rute-nya sendiri sudah bermiddleware auth.
*/

$prefix = 'nawasara-docs';

return [
    [
        'workspace' => 'nawasara-docs',
        'label' => 'Dokumentasi',
        'icon' => 'lucide-book-open',
        'group' => 'Pengaturan',
        'url' => '',
        'permission' => null,
        'submenu' => [
            [
                'label' => 'Ikhtisar',
                'icon' => 'lucide-compass',
                'url' => url($prefix),
                'permission' => null,
                'navigate' => true,
            ],
            [
                'label' => 'Komponen UI',
                'icon' => 'lucide-layout-grid',
                'url' => url($prefix.'/components'),
                'permission' => null,
                'navigate' => true,
            ],
            [
                'label' => 'Referensi API',
                'icon' => 'lucide-plug',
                'url' => url($prefix.'/api'),
                'permission' => null,
                'navigate' => true,
            ],
            [
                'label' => 'Install Agent',
                'icon' => 'lucide-server',
                'url' => url($prefix.'/guides/install-agent'),
                'permission' => null,
                'navigate' => true,
            ],
            [
                'label' => 'Buat Package',
                'icon' => 'lucide-package-plus',
                'url' => url($prefix.'/guides/package'),
                'permission' => null,
                'navigate' => true,
            ],
        ],
    ],
];
