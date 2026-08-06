<?php

namespace Nawasara\Docs\Support;

/**
 * Cuplikan preview per komponen — markup yang dirender hidup di samping
 * kodenya di halaman katalog.
 *
 * Ditulis tangan, bukan diturunkan dari `@props`, karena komponen yang berguna
 * hampir selalu butuh isi yang bermakna: sebuah badge tanpa teks atau tabel
 * tanpa baris memang bisa dirender, tapi tidak menunjukkan apa pun.
 *
 * Komponen yang TIDAK terdaftar di sini sengaja tidak dipreview. Sebagian
 * memang tidak bisa: layout adalah kerangka halaman, modal butuh pemicu,
 * command-palette membaca daftar menu dari container. Menampilkannya sebagai
 * kotak kosong akan lebih membingungkan daripada tidak menampilkan apa-apa.
 */
class ComponentPreviews
{
    /**
     * @return array<string, string> nama komponen => markup blade
     */
    public function all(): array
    {
        return [
            'badge' => <<<'BLADE'
                <div class="flex flex-wrap items-center gap-2">
                    <x-nawasara-ui::badge color="success">Aktif</x-nawasara-ui::badge>
                    <x-nawasara-ui::badge color="warning">Menunggu</x-nawasara-ui::badge>
                    <x-nawasara-ui::badge color="danger" variant="solid">Ditangguhkan</x-nawasara-ui::badge>
                    <x-nawasara-ui::badge color="info" variant="outline">Draf</x-nawasara-ui::badge>
                    <x-nawasara-ui::badge color="neutral" dot>Nonaktif</x-nawasara-ui::badge>
                </div>
                BLADE,

            'button' => <<<'BLADE'
                <div class="flex flex-wrap items-center gap-2">
                    <x-nawasara-ui::button color="primary">Simpan</x-nawasara-ui::button>
                    <x-nawasara-ui::button color="success" variant="outline">Setujui</x-nawasara-ui::button>
                    <x-nawasara-ui::button color="danger" variant="ghost">Hapus</x-nawasara-ui::button>
                    <x-nawasara-ui::button color="neutral" size="sm">Kecil</x-nawasara-ui::button>
                    <x-nawasara-ui::button color="primary" :disabled="true">Nonaktif</x-nawasara-ui::button>
                </div>
                BLADE,

            'icon-button' => <<<'BLADE'
                <div class="flex items-center gap-2">
                    <x-nawasara-ui::icon-button icon="refresh-cw" tooltip="Muat ulang" />
                    <x-nawasara-ui::icon-button icon="pencil" tooltip="Ubah" />
                    <x-nawasara-ui::icon-button icon="trash-2" tooltip="Hapus" />
                </div>
                BLADE,

            'stat-card' => <<<'BLADE'
                <div class="grid gap-3 sm:grid-cols-3">
                    <x-nawasara-ui::stat-card compact title="Total OPD" value="16" subtitle="terdaftar" icon="lucide-building-2" color="blue" />
                    <x-nawasara-ui::stat-card compact title="Aset" value="281" subtitle="domain & subdomain" icon="lucide-globe" color="emerald" />
                    <x-nawasara-ui::stat-card compact title="Insiden" value="7" subtitle="24 jam terakhir" icon="lucide-shield-alert" color="amber" />
                </div>
                BLADE,

            'empty-state' => <<<'BLADE'
                <x-nawasara-ui::empty-state
                    icon="lucide-inbox"
                    title="Belum ada data"
                    description="Data akan muncul di sini setelah sinkronisasi pertama berjalan." />
                BLADE,

            'segmented-control' => <<<'BLADE'
                <div x-data="{ tab: 'semua' }">
                    <x-nawasara-ui::segmented-control
                        :options="['semua' => 'Semua', 'aktif' => 'Aktif', 'arsip' => 'Arsip']"
                        x-model="tab" />
                </div>
                BLADE,

            'toggle' => <<<'BLADE'
                <div class="flex items-center gap-6" x-data="{ a: true, b: false }">
                    <x-nawasara-ui::toggle x-model="a" label="Aktif" />
                    <x-nawasara-ui::toggle x-model="b" label="Nonaktif" />
                </div>
                BLADE,

            'tooltip' => <<<'BLADE'
                <x-nawasara-ui::tooltip text="Keterangan muncul saat kursor di atas">
                    <span class="cursor-help border-b border-dashed border-neutral-400 text-sm text-neutral-700 dark:text-neutral-300">
                        Arahkan kursor ke sini
                    </span>
                </x-nawasara-ui::tooltip>
                BLADE,

            'loading' => '<x-nawasara-ui::loading />',

            'skeleton' => <<<'BLADE'
                <div class="space-y-2">
                    <x-nawasara-ui::skeleton class="h-4 w-3/4" />
                    <x-nawasara-ui::skeleton class="h-4 w-1/2" />
                    <x-nawasara-ui::skeleton class="h-4 w-2/3" />
                </div>
                BLADE,

            'skeleton-table' => '<x-nawasara-ui::skeleton-table :rows="3" :cols="4" />',

            'skeleton-stats' => '<x-nawasara-ui::skeleton-stats :count="3" />',

            'search-input' => '<x-nawasara-ui::search-input placeholder="Cari data…" />',

            'page-header' => <<<'BLADE'
                <x-nawasara-ui::page-header
                    title="Daftar OPD"
                    description="Organisasi perangkat daerah yang terdaftar."
                    count="16 OPD">
                    <x-nawasara-ui::button color="primary" size="sm">Tambah</x-nawasara-ui::button>
                </x-nawasara-ui::page-header>
                BLADE,

            'page.card' => <<<'BLADE'
                <x-nawasara-ui::page.card>
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        Kartu adalah wadah dasar untuk isi halaman.
                    </p>
                </x-nawasara-ui::page.card>
                BLADE,

            'form.input' => null,   // butuh konteks Livewire — lihat catatan kelas
            'form.select' => null,
            'form.textarea' => null,
            'form.checkbox' => null,
            'form.radio' => null,

            'form.label' => '<x-nawasara-ui::form.label value="Nama Lengkap" />',

            'button-group' => <<<'BLADE'
                <x-nawasara-ui::button-group>
                    <x-nawasara-ui::button-group.item>Harian</x-nawasara-ui::button-group.item>
                    <x-nawasara-ui::button-group.item>Mingguan</x-nawasara-ui::button-group.item>
                    <x-nawasara-ui::button-group.item>Bulanan</x-nawasara-ui::button-group.item>
                </x-nawasara-ui::button-group>
                BLADE,

            'dark-mode-toggle' => '<x-nawasara-ui::dark-mode-toggle />',
        ];
    }

    /**
     * Markup preview untuk sebuah komponen, atau null bila tidak ada.
     *
     * Nilai null yang tercatat eksplisit di daftar diperlakukan sama dengan
     * tidak terdaftar — itu penanda "sudah dipertimbangkan, sengaja tidak
     * dipreview", supaya orang berikutnya tidak menghabiskan waktu mencoba.
     */
    public function for(string $name): ?string
    {
        $all = $this->all();

        return $all[$name] ?? null;
    }

    /** Berapa komponen yang punya preview. */
    public function count(): int
    {
        return count(array_filter($this->all()));
    }
}
