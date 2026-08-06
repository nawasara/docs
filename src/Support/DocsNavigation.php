<?php

namespace Nawasara\Docs\Support;

/**
 * Struktur navigasi docs — satu sumber untuk sidebar aplikasi, halaman
 * indeks, dan config/menu.php, supaya ketiganya tidak bisa berbeda.
 *
 * `path` disimpan terpisah dari `route` karena config/menu.php di-load sebelum
 * route terdaftar, jadi ia tidak bisa memanggil route().
 */
class DocsNavigation
{
    /** Prefix URL, sama dengan yang dipakai routes/web.php. */
    public const PREFIX = 'nawasara-docs';

    /**
     * @return array<int, array{
     *   label:string, icon:string, description:string,
     *   items:array<int, array{label:string, route:string, path:string, icon:string, description:string}>
     * }>
     */
    public function sections(): array
    {
        return [
            [
                'label' => 'Mulai',
                'icon' => 'lucide-compass',
                'description' => 'Orientasi untuk yang baru bergabung.',
                'items' => [
                    [
                        'label' => 'Ikhtisar',
                        'route' => 'nawasara-docs.index',
                        'path' => self::PREFIX,
                        'icon' => 'lucide-compass',
                        'description' => 'Apa itu Nawasara dan bagaimana bagian-bagiannya tersusun.',
                    ],
                ],
            ],
            [
                'label' => 'Komponen UI',
                'icon' => 'lucide-layout-grid',
                'description' => 'Komponen Blade bersama dari nawasara/ui.',
                'items' => [
                    [
                        'label' => 'Katalog komponen',
                        'route' => 'nawasara-docs.components.index',
                        'path' => self::PREFIX.'/components',
                        'icon' => 'lucide-layout-grid',
                        'description' => 'Seluruh komponen beserta prop, contoh, dan tampilan langsungnya.',
                    ],
                    [
                        'label' => 'Galeri langsung',
                        'route' => 'nawasara-docs.components.gallery',
                        'path' => self::PREFIX.'/components/gallery',
                        'icon' => 'lucide-eye',
                        'description' => 'Tampilan hidup: tombol, modal, toaster.',
                    ],
                ],
            ],
            [
                'label' => 'API',
                'icon' => 'lucide-plug',
                'description' => 'Endpoint publik, scope, dan cara memakai token.',
                'items' => [
                    [
                        'label' => 'Referensi endpoint',
                        'route' => 'nawasara-docs.api.index',
                        'path' => self::PREFIX.'/api',
                        'icon' => 'lucide-list',
                        'description' => 'Semua endpoint terdaftar beserta scope yang dibutuhkan.',
                    ],
                    [
                        'label' => 'Autentikasi & token',
                        'route' => 'nawasara-docs.api.auth',
                        'path' => self::PREFIX.'/api/auth',
                        'icon' => 'lucide-key',
                        'description' => 'Cara membuat token, memakainya, dan membatasi aksesnya.',
                    ],
                    [
                        'label' => 'Panduan per domain',
                        'route' => 'nawasara-docs.api.domains',
                        'path' => self::PREFIX.'/api/domains',
                        'icon' => 'lucide-book-marked',
                        'description' => 'Contoh request dan respons untuk tiap domain: registry, zoom, secscan, keycloak, cctv, wifi.',
                    ],
                ],
            ],
            [
                'label' => 'Panduan',
                'icon' => 'lucide-book-open',
                'description' => 'Prosedur yang ditulis untuk dijalankan.',
                'items' => [
                    [
                        'label' => 'Install agent secscan',
                        'route' => 'nawasara-docs.guides.agent',
                        'path' => self::PREFIX.'/guides/install-agent',
                        'icon' => 'lucide-server',
                        'description' => 'Memasang nawasara-agent di server OPD.',
                    ],
                    [
                        'label' => 'Membuat package baru',
                        'route' => 'nawasara-docs.guides.package',
                        'path' => self::PREFIX.'/guides/package',
                        'icon' => 'lucide-package-plus',
                        'description' => 'Konvensi scaffold dan wiring yang wajib.',
                    ],
                    [
                        'label' => 'Menambah endpoint API',
                        'route' => 'nawasara-docs.guides.api',
                        'path' => self::PREFIX.'/guides/add-api',
                        'icon' => 'lucide-plug-zap',
                        'description' => 'Menempelkan endpoint dan scope baru ke package yang sudah ada.',
                    ],
                ],
            ],
        ];
    }

    /** Semua item diratakan — untuk pencarian dan penghitungan. */
    public function items(): array
    {
        $out = [];

        foreach ($this->sections() as $section) {
            foreach ($section['items'] as $item) {
                $out[] = $item + ['section' => $section['label']];
            }
        }

        return $out;
    }
}
