{{--
    Kerangka halaman docs.

    Navigasi docs hidup di sidebar aplikasi (lihat config/menu.php, yang
    dibangun dari DocsNavigation), bukan di dalam halaman. Sebelumnya keduanya
    ada dan isinya sama persis — pengulangan yang memakan ruang baca tanpa
    memberi apa pun.

    Pemakaian:
        <x-nawasara-docs::shell title="Referensi API" :breadcrumb="[['label' => 'API']]">
            <x-slot:lead>Kalimat pengantar opsional.</x-slot:lead>
            ... isi halaman ...
        </x-nawasara-docs::shell>
--}}
@props([
    'title',
    'lead' => null,
    'breadcrumb' => [],
])

@php
    $crumbs = array_merge(
        [['label' => 'Dokumentasi', 'url' => route('nawasara-docs.index')]],
        $breadcrumb,
    );
@endphp

<x-nawasara-ui::layouts.app>
    <x-slot:title>{{ $title }} — Dokumentasi Nawasara</x-slot:title>

    <x-slot name="breadcrumb">
        <livewire:nawasara-ui.shared-components.breadcrumb :items="$crumbs" />
    </x-slot>

    <x-nawasara-ui::page.container>
        {{-- max-w membatasi panjang baris teks. Tanpa itu, di layar lebar
             paragraf jadi terlalu panjang untuk diikuti mata dengan nyaman. --}}
        <div class="max-w-5xl">
            <header class="mb-5">
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-50">{{ $title }}</h1>
                @if ($lead)
                    <p class="mt-1.5 text-sm text-neutral-600 dark:text-neutral-300">{{ $lead }}</p>
                @endif
            </header>

            {{ $slot }}
        </div>
    </x-nawasara-ui::page.container>
</x-nawasara-ui::layouts.app>
