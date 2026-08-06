<?php

namespace Nawasara\Docs\Support;

/**
 * Struktur navigasi docs — satu sumber untuk sidebar dan halaman indeks,
 * supaya keduanya tidak bisa berbeda.
 */
class DocsNavigation
{
    /**
     * @return array<int, array{
     *   label:string, icon:string, description:string,
     *   items:array<int, array{label:string, route:string, params?:array, description:string}>
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
                        'description' => 'Seluruh komponen beserta prop dan contoh pemakaiannya.',
                    ],
                    [
                        'label' => 'Galeri langsung',
                        'route' => 'nawasara-docs.components.gallery',
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
                        'description' => 'Semua endpoint terdaftar beserta scope yang dibutuhkan.',
                    ],
                    [
                        'label' => 'Autentikasi & token',
                        'route' => 'nawasara-docs.api.auth',
                        'description' => 'Cara membuat token, memakainya, dan membatasi aksesnya.',
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
                        'description' => 'Memasang nawasara-agent di server OPD.',
                    ],
                    [
                        'label' => 'Membuat package baru',
                        'route' => 'nawasara-docs.guides.package',
                        'description' => 'Konvensi scaffold dan wiring yang wajib.',
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
