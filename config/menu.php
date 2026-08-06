<?php

/*
| Menu sidebar untuk Dokumentasi.
|
| SATU workspace dengan submenu datar, bukan satu entri per bagian.
| WorkspaceManager menggabungkan entri ber-id sama dan mengambil label dari
| yang pertama ter-load, jadi mendaftarkan tiap bagian sebagai workspace
| terpisah menghasilkan sidebar berjudul "Mulai" berisi seluruh halaman —
| dan submenu-nya terduplikasi karena file ini terbaca dari dua path
| (packages/ dan vendor/, keduanya menunjuk berkas yang sama).
|
| Daftar halaman dibangun dari DocsNavigation supaya sidebar dan halaman
| indeks tidak bisa berbeda; cukup satu tempat yang disunting.
|
| Tanpa `permission`: isinya cara memakai sistem, bukan datanya. Rutenya
| sendiri sudah bermiddleware auth.
*/

use Nawasara\Docs\Support\DocsNavigation;

// Config dimuat sebelum container siap, jadi instansiasi langsung.
// DocsNavigation memang tidak punya dependency.
$nav = new DocsNavigation();

$submenu = [];

foreach ($nav->sections() as $section) {
    foreach ($section['items'] as $item) {
        // route() belum tentu tersedia saat config di-cache, jadi URL disusun
        // dari path yang sama dengan yang dipakai routes/web.php.
        $submenu[] = [
            'label' => $item['label'],
            'icon' => $item['icon'] ?? 'lucide-file-text',
            'url' => url($item['path']),
            'permission' => null,
            'navigate' => true,
        ];
    }
}

return [
    [
        'workspace' => 'nawasara-docs',
        'label' => 'Dokumentasi',
        'icon' => 'lucide-book-open',
        'group' => 'Pengaturan',
        'url' => '',
        'permission' => null,
        'submenu' => $submenu,
    ],
];
