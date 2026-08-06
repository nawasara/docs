@php
    $catalog = app(\Nawasara\Docs\Support\ComponentCatalog::class);
    $previews = app(\Nawasara\Docs\Support\ComponentPreviews::class);
    $grouped = $catalog->grouped();
    $stats = $catalog->stats();

    $groupLabels = [
        'root' => 'Umum',
        'form' => 'Formulir',
        'page' => 'Kerangka Halaman',
        'button-group' => 'Grup Tombol',
    ];
@endphp

<x-nawasara-docs::shell
    title="Katalog Komponen"
    :breadcrumb="[['label' => 'Komponen UI']]"
    lead="Dibaca langsung dari file blade di nawasara/ui, jadi komponen baru muncul sendiri tanpa halaman ini perlu disunting.">

    <div class="mb-5 flex flex-wrap items-center gap-2 text-sm">
        <x-nawasara-ui::badge color="blue">{{ $stats['total'] }} komponen</x-nawasara-ui::badge>
        <x-nawasara-ui::badge color="success">{{ $stats['documented'] }} terdokumentasi</x-nawasara-ui::badge>
        @if ($stats['total'] > $stats['documented'])
            <x-nawasara-ui::badge color="warning">{{ $stats['total'] - $stats['documented'] }} belum</x-nawasara-ui::badge>
        @endif
        <a href="{{ route('nawasara-docs.components.gallery') }}" wire:navigate
            class="ml-auto text-sm text-emerald-600 hover:underline dark:text-emerald-400">Lihat galeri langsung &rarr;</a>
    </div>

    @if ($stats['total'] === 0)
        <x-nawasara-ui::empty-state
            icon="lucide-package-x"
            title="Komponen tidak terbaca"
            description="Folder komponen nawasara/ui tidak ditemukan. Pastikan package terpasang." />
    @endif

    {{-- Pencarian dilakukan di sisi klien: seluruh katalog sudah ada di
         halaman, jadi menambah perjalanan ke server hanya membuat pengetikan
         terasa lambat tanpa memberi apa pun. --}}
    <div x-data="{ q: '' }">
        {{-- Input native, bukan x-nawasara-ui::form.input: komponen itu
             mengandalkan konteks Livewire untuk error handling dan tidak bisa
             dirender di halaman statis. Pencarian di sini murni sisi klien,
             jadi tidak ada state server yang perlu diikat. --}}
        <div class="relative mb-4">
            <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-neutral-400" />
            <input type="search" x-model="q" autocomplete="off"
                placeholder="Cari komponen (mis. badge, form.input, table)…"
                class="w-full rounded-lg border border-neutral-300 bg-white py-2 pl-9 pr-3 text-sm text-neutral-800 placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500" />
        </div>

        @foreach ($grouped as $group => $components)
            <section class="mb-6" x-show="$el.querySelectorAll('[data-item]:not([hidden])').length > 0">
                <h2 class="mb-2.5 text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                    {{ $groupLabels[$group] ?? ucfirst($group) }}
                    <span class="ml-1 font-normal normal-case text-neutral-400">({{ count($components) }})</span>
                </h2>

                <div class="space-y-2.5">
                    @foreach ($components as $c)
                        <div data-item
                            x-bind:hidden="q !== '' && ! '{{ strtolower($c['name']) }}'.includes(q.toLowerCase())">
                            <x-nawasara-ui::page.card>
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <code class="text-sm font-semibold text-neutral-900 dark:text-neutral-50">{{ $c['tag'] }}</code>
                                        @if ($c['description'])
                                            <p class="mt-1 max-w-3xl whitespace-pre-line text-sm text-neutral-600 dark:text-neutral-300">{{ $c['description'] }}</p>
                                        @else
                                            <p class="mt-1 text-sm italic text-neutral-400 dark:text-neutral-500">
                                                Belum ada keterangan di file komponennya.
                                            </p>
                                        @endif
                                    </div>

                                    @unless ($c['documented'])
                                        <x-nawasara-ui::badge color="warning">Belum didokumentasikan</x-nawasara-ui::badge>
                                    @endunless
                                </div>

                                @if ($c['props'])
                                    <div class="mt-3">
                                        <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Prop</p>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="border-b border-neutral-200 text-left dark:border-neutral-700">
                                                        <th class="py-1.5 pr-4 font-medium text-neutral-600 dark:text-neutral-300">Nama</th>
                                                        <th class="py-1.5 pr-4 font-medium text-neutral-600 dark:text-neutral-300">Default</th>
                                                        <th class="py-1.5 font-medium text-neutral-600 dark:text-neutral-300">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                                    @foreach ($c['props'] as $p)
                                                        <tr>
                                                            <td class="py-1.5 pr-4 align-top"><code class="text-xs text-neutral-800 dark:text-neutral-200">{{ $p['name'] }}</code></td>
                                                            <td class="py-1.5 pr-4 align-top">
                                                                @if ($p['default'] !== null)
                                                                    <code class="text-xs text-neutral-500 dark:text-neutral-400">{{ $p['default'] }}</code>
                                                                @else
                                                                    <span class="text-xs text-neutral-400">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-1.5 align-top text-xs text-neutral-600 dark:text-neutral-300">{{ $p['comment'] ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @php $preview = $previews->for($c['name']); @endphp

                                @if ($preview)
                                    {{-- Preview sekaligus menampilkan sumbernya, jadi
                                         blok "Pemakaian" di bawah tidak perlu diulang. --}}
                                    <x-nawasara-docs::preview :code="$preview" />
                                @elseif ($c['usage'])
                                    <div class="mt-3">
                                        <x-nawasara-docs::code lang="blade" label="Pemakaian">{{ $c['usage'] }}</x-nawasara-docs::code>
                                    </div>
                                @endif

                                <p class="mt-2.5 font-mono text-[11px] text-neutral-400 dark:text-neutral-500">{{ $c['path'] }}</p>
                            </x-nawasara-ui::page.card>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <p class="text-sm text-neutral-500 dark:text-neutral-400" x-show="q !== ''" x-cloak>
            Tidak menemukan yang dicari? Komponen dibaca dari
            <code class="text-xs">packages/nawasara-ui/resources/views/components/</code>.
        </p>
    </div>
</x-nawasara-docs::shell>
