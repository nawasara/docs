{{--
    Kerangka halaman docs: layout aplikasi + sidebar navigasi docs.

    Sidebar-nya terpisah dari sidebar workspace utama karena docs punya
    strukturnya sendiri (bagian → halaman) yang tidak cocok dipaksakan ke
    bentuk workspace/submenu.

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
    $nav = app(\Nawasara\Docs\Support\DocsNavigation::class);
    $current = request()->route()?->getName();
    $crumbs = array_merge([['label' => 'Dokumentasi', 'url' => route('nawasara-docs.index')]], $breadcrumb);
@endphp

<x-nawasara-ui::layouts.app>
    <x-slot:title>{{ $title }} — Dokumentasi Nawasara</x-slot:title>

    <x-slot name="breadcrumb">
        <livewire:nawasara-ui.shared-components.breadcrumb :items="$crumbs" />
    </x-slot>

    <x-nawasara-ui::page.container>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">

            {{-- Navigasi docs. Di layar sempit ia menjadi daftar biasa di atas
                 konten; sticky hanya di lebar besar, di mana ada ruang. --}}
            <aside class="w-full shrink-0 lg:sticky lg:top-20 lg:w-60">
                <nav class="space-y-5">
                    @foreach ($nav->sections() as $section)
                        <div>
                            <p class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                <x-dynamic-component :component="$section['icon']" class="size-3.5" />
                                {{ $section['label'] }}
                            </p>
                            <ul class="space-y-0.5">
                                @foreach ($section['items'] as $item)
                                    @php $active = $current === $item['route']; @endphp
                                    <li>
                                        <a href="{{ route($item['route']) }}" wire:navigate
                                            @class([
                                                'block rounded-lg px-2.5 py-1.5 text-sm transition-colors',
                                                'bg-emerald-50 font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' => $active,
                                                'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800' => ! $active,
                                            ])>
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="mb-5">
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-50">{{ $title }}</h1>
                    @if ($lead)
                        <p class="mt-1.5 max-w-3xl text-sm text-neutral-600 dark:text-neutral-300">{{ $lead }}</p>
                    @endif
                </header>

                {{ $slot }}
            </div>
        </div>
    </x-nawasara-ui::page.container>
</x-nawasara-ui::layouts.app>
