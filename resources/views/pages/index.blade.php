@php
    $nav = app(\Nawasara\Docs\Support\DocsNavigation::class);
    $components = app(\Nawasara\Docs\Support\ComponentCatalog::class)->stats();
    $api = app(\Nawasara\Docs\Support\ApiCatalog::class)->stats();
@endphp

<x-nawasara-docs::shell
    title="Dokumentasi Nawasara"
    lead="Rujukan internal untuk komponen UI, API, dan prosedur pemasangan. Angka di halaman ini dibaca langsung dari kode yang berjalan, bukan dari catatan terpisah.">

    <div class="mb-6 grid gap-3 sm:grid-cols-3">
        <x-nawasara-ui::stat-card compact
            title="Komponen UI"
            :value="$components['total']"
            :subtitle="$components['documented'].' terdokumentasi'"
            icon="lucide-layout-grid"
            color="blue" />

        <x-nawasara-ui::stat-card compact
            title="Endpoint API"
            :value="$api['endpoints']"
            :subtitle="$api['domains'].' domain'"
            icon="lucide-plug"
            color="emerald" />

        <x-nawasara-ui::stat-card compact
            title="Scope API"
            :value="$api['scopes']"
            subtitle="dipakai untuk membatasi token"
            icon="lucide-key"
            color="amber" />
    </div>

    <div class="space-y-4">
        @foreach ($nav->sections() as $section)
            @continue($section['label'] === 'Mulai')

            <x-nawasara-ui::page.card>
                <div class="mb-3 flex items-start gap-2.5">
                    <x-dynamic-component :component="$section['icon']" class="mt-0.5 size-5 text-neutral-500 dark:text-neutral-400" />
                    <div>
                        <h2 class="text-base font-semibold text-neutral-900 dark:text-neutral-50">{{ $section['label'] }}</h2>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">{{ $section['description'] }}</p>
                    </div>
                </div>

                <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($section['items'] as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" wire:navigate
                                class="flex items-center justify-between gap-3 py-2.5 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $item['label'] }}</span>
                                    <span class="block truncate text-xs text-neutral-500 dark:text-neutral-400">{{ $item['description'] }}</span>
                                </span>
                                <x-lucide-chevron-right class="size-4 shrink-0 text-neutral-400" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-nawasara-ui::page.card>
        @endforeach
    </div>
</x-nawasara-docs::shell>
