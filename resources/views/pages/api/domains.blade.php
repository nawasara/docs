@php
    $catalog = app(\Nawasara\Docs\Support\ApiCatalog::class);
    $examples = app(\Nawasara\Docs\Support\ApiExamples::class);

    $grouped = $catalog->grouped();
    $scopes = $catalog->scopes();

    // Domain yang punya penjelasan tertulis didahulukan; sisanya (mis. `meta`)
    // tetap ditampilkan supaya tidak ada endpoint yang tersembunyi dari
    // halaman ini hanya karena belum sempat ditulis narasinya.
    $ordered = [];
    foreach (array_keys($examples->all()) as $d) {
        if (isset($grouped[$d])) $ordered[$d] = $grouped[$d];
    }
    foreach ($grouped as $d => $routes) {
        if (! isset($ordered[$d])) $ordered[$d] = $routes;
    }

    $methodColor = fn (string $m) => match ($m) {
        'GET' => 'info', 'POST' => 'success',
        'PUT', 'PATCH' => 'warning', 'DELETE' => 'danger',
        default => 'neutral',
    };
@endphp

<x-nawasara-docs::shell
    title="Panduan API per Domain"
    :breadcrumb="[['label' => 'API', 'url' => route('nawasara-docs.api.index')], ['label' => 'Panduan per domain']]"
    lead="Untuk tiap domain: apa gunanya, contoh request dan respons, serta apa yang sengaja tidak dikembalikan.">

    {{-- Tautan lompat: halaman ini panjang, dan tanpa ini pembaca harus
         menggulir melewati domain yang tidak dia butuhkan. --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($ordered as $domain => $routes)
            <a href="#{{ $domain }}"
                class="rounded-lg border border-neutral-200 px-2.5 py-1 text-sm text-neutral-700 transition-colors hover:border-emerald-400 hover:text-emerald-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:text-emerald-400">
                {{ $examples->for($domain)['title'] ?? ucfirst($domain) }}
                <span class="ml-0.5 text-xs text-neutral-400">{{ count($routes) }}</span>
            </a>
        @endforeach
    </div>

    <div class="space-y-8">
        @foreach ($ordered as $domain => $routes)
            @php $info = $examples->for($domain); @endphp

            <section id="{{ $domain }}" class="scroll-mt-20">
                <div class="mb-3 flex items-start gap-2.5">
                    <x-dynamic-component :component="$info['icon'] ?? 'lucide-plug'"
                        class="mt-0.5 size-5 shrink-0 text-neutral-500 dark:text-neutral-400" />
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">
                            {{ $info['title'] ?? ucfirst($domain) }}
                        </h2>
                        @if (! empty($info['summary']))
                            <p class="mt-0.5 text-sm text-neutral-600 dark:text-neutral-300">{{ $info['summary'] }}</p>
                        @endif
                    </div>
                </div>

                {{-- Endpoint: dari tabel route, bukan ditulis tangan. --}}
                <x-nawasara-ui::page.card>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Endpoint</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @foreach ($routes as $r)
                                    <tr>
                                        <td class="py-1.5 pr-3 align-top whitespace-nowrap">
                                            @foreach ($r['methods'] as $m)
                                                <x-nawasara-ui::badge :color="$methodColor($m)">{{ $m }}</x-nawasara-ui::badge>
                                            @endforeach
                                        </td>
                                        <td class="py-1.5 pr-3 align-top">
                                            <code class="text-xs text-neutral-800 dark:text-neutral-200">/{{ $r['uri'] }}</code>
                                        </td>
                                        <td class="py-1.5 align-top">
                                            @forelse ($r['scopes'] as $s)
                                                <code class="block text-xs text-emerald-700 dark:text-emerald-400">{{ $s }}</code>
                                            @empty
                                                <span class="text-xs text-neutral-400">—</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-nawasara-ui::page.card>

                @if (! empty($info['notes']))
                    <div class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-3 dark:border-sky-800 dark:bg-sky-900/20">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-sky-800 dark:text-sky-200">Yang perlu diketahui</p>
                        <ul class="space-y-1 text-sm text-sky-900 dark:text-sky-100">
                            @foreach ($info['notes'] as $n)
                                <li class="flex gap-1.5">
                                    <span class="text-sky-400">&bull;</span>
                                    <span>{!! \Illuminate\Support\Str::of(e($n))->replaceMatches('/`([^`]+)`/', '<code class="text-xs">$1</code>') !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($info['withheld']))
                    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-200">Tidak pernah dikembalikan</p>
                        <ul class="space-y-1 text-sm text-amber-900 dark:text-amber-100">
                            @foreach ($info['withheld'] as $w)
                                <li class="flex gap-1.5">
                                    <span class="text-amber-400">&bull;</span>
                                    <span>{!! \Illuminate\Support\Str::of(e($w))->replaceMatches('/`([^`]+)`/', '<code class="text-xs">$1</code>') !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($info['examples']))
                    <div class="mt-3 space-y-3">
                        @foreach ($info['examples'] as $ex)
                            <x-nawasara-ui::page.card>
                                <p class="mb-2 text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $ex['label'] }}</p>
                                <div class="space-y-2">
                                    <x-nawasara-docs::code lang="http" label="Request">{{ $ex['request'] }}</x-nawasara-docs::code>
                                    <x-nawasara-docs::code lang="json" label="Respons">{{ $ex['response'] }}</x-nawasara-docs::code>
                                </div>
                            </x-nawasara-ui::page.card>
                        @endforeach
                    </div>
                @endif

                @if (! empty($scopes[$domain]))
                    <div class="mt-3">
                        <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Scope</p>
                        <x-nawasara-ui::page.card>
                            <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @foreach ($scopes[$domain] as $s)
                                    <li class="py-2 first:pt-0 last:pb-0">
                                        <code class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ $s['name'] }}</code>
                                        <p class="mt-0.5 text-sm text-neutral-600 dark:text-neutral-300">{{ $s['description'] }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </x-nawasara-ui::page.card>
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-nawasara-docs::shell>
