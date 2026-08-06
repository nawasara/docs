{{--
    Halaman panduan generik: merender file markdown yang sudah ada di repo.

    Variabel yang diharapkan: $title, $lead, $source, $guide, $breadcrumb.
--}}
<x-nawasara-docs::shell :title="$title" :lead="$lead" :breadcrumb="$breadcrumb">

    @if ($guide['missing'])
        <x-nawasara-ui::empty-state
            icon="lucide-file-question"
            title="Sumber panduan tidak ditemukan"
            :description="'Berkas '.$source.' tidak ada. Kemungkinan package-nya belum terpasang atau berkasnya dipindahkan.'" />
    @else
        <div class="flex flex-col gap-6 xl:flex-row-reverse xl:items-start">

            @if (count($guide['toc']) > 2)
                <aside class="w-full shrink-0 xl:sticky xl:top-20 xl:w-56">
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Isi halaman</p>
                    <ul class="space-y-0.5 border-l border-neutral-200 dark:border-neutral-700">
                        @foreach ($guide['toc'] as $item)
                            <li>
                                <a href="#{{ $item['anchor'] }}"
                                    @class([
                                        'block border-l-2 border-transparent py-0.5 text-sm text-neutral-600 transition-colors hover:border-emerald-400 hover:text-emerald-700 dark:text-neutral-400 dark:hover:text-emerald-400',
                                        'pl-2.5' => $item['level'] === 1,
                                        'pl-4' => $item['level'] === 2,
                                        'pl-6 text-xs' => $item['level'] >= 3,
                                    ])>
                                    {{ $item['text'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            @endif

            <div class="min-w-0 flex-1">
                <x-nawasara-ui::page.card>
                    {{-- Kelas prose ditulis eksplisit di sini, bukan lewat plugin
                         typography: markup datang dari CommonMark yang sudah
                         meng-escape input, dan menuliskannya sendiri membuat
                         tampilannya sejalan dengan komponen lain di aplikasi. --}}
                    <div class="docs-prose">
                        {!! $guide['html'] !!}
                    </div>
                </x-nawasara-ui::page.card>

                <p class="mt-2.5 text-xs text-neutral-500 dark:text-neutral-400">
                    Sumber: <code class="font-mono">{{ $source }}</code> — disunting di sana, bukan di halaman ini.
                </p>
            </div>
        </div>
    @endif
</x-nawasara-docs::shell>
