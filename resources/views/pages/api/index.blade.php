@php
    $catalog = app(\Nawasara\Docs\Support\ApiCatalog::class);
    $grouped = $catalog->grouped();
    $scopes = $catalog->scopes();
    $stats = $catalog->stats();
    $mismatch = $catalog->scopeMismatches();

    $methodColor = fn (string $m) => match ($m) {
        'GET' => 'info',
        'POST' => 'success',
        'PUT', 'PATCH' => 'warning',
        'DELETE' => 'danger',
        default => 'neutral',
    };
@endphp

<x-nawasara-docs::shell
    title="Referensi API"
    :breadcrumb="[['label' => 'API']]"
    lead="Daftar ini dibangun dari tabel route dan registry scope saat halaman dibuka — yang tertulis di sini persis yang ditegakkan server.">

    <div class="mb-5 flex flex-wrap items-center gap-2">
        <x-nawasara-ui::badge color="blue">{{ $stats['endpoints'] }} endpoint</x-nawasara-ui::badge>
        <x-nawasara-ui::badge color="success">{{ $stats['domains'] }} domain</x-nawasara-ui::badge>
        <x-nawasara-ui::badge color="warning">{{ $stats['scopes'] }} scope</x-nawasara-ui::badge>
        <a href="{{ route('nawasara-docs.api.auth') }}" wire:navigate
            class="ml-auto text-sm text-emerald-600 hover:underline dark:text-emerald-400">Cara pakai token &rarr;</a>
    </div>

    {{-- Ketidakcocokan scope ditampilkan menonjol karena keduanya adalah
         kekeliruan yang tidak memunculkan error di mana pun: yang satu bikin
         endpoint terbuka lebih lebar dari yang dikira, yang satu bikin
         endpoint terkunci untuk semua orang. --}}
    @if ($mismatch['unregistered'])
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-3 dark:border-rose-800 dark:bg-rose-900/20">
            <p class="text-sm font-medium text-rose-800 dark:text-rose-200">
                Scope dipakai route tapi belum terdaftar
            </p>
            <p class="mt-0.5 text-xs text-rose-700 dark:text-rose-300">
                UI token tidak bisa memberikan scope yang tak terdaftar, jadi endpoint ini
                terkunci untuk semua token. Daftarkan lewat <code>registerApiScopes()</code> di ServiceProvider package.
            </p>
            <p class="mt-1.5 font-mono text-xs text-rose-800 dark:text-rose-200">{{ implode(', ', $mismatch['unregistered']) }}</p>
        </div>
    @endif

    @if ($mismatch['unused'])
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                Scope terdaftar tapi tidak dipakai route mana pun
            </p>
            <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-300">
                Bisa berarti middleware <code>scope:</code> lupa dipasang — endpoint-nya terbuka
                untuk token mana pun yang lolos autentikasi.
            </p>
            <p class="mt-1.5 font-mono text-xs text-amber-800 dark:text-amber-200">{{ implode(', ', $mismatch['unused']) }}</p>
        </div>
    @endif

    @if ($stats['endpoints'] === 0)
        <x-nawasara-ui::empty-state
            icon="lucide-plug-zap"
            title="Belum ada endpoint terdaftar"
            description="Package nawasara/api mungkin belum terpasang, atau belum ada package yang memuat routes/api.php." />
    @endif

    @foreach ($grouped as $domain => $routes)
        <section class="mb-5">
            <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                {{ $domain }}
                <span class="ml-1 font-normal normal-case text-neutral-400">({{ count($routes) }} endpoint)</span>
            </h2>

            <x-nawasara-ui::page.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-neutral-200 text-left dark:border-neutral-700">
                                <th class="py-2 pr-3 font-medium text-neutral-600 dark:text-neutral-300">Method</th>
                                <th class="py-2 pr-3 font-medium text-neutral-600 dark:text-neutral-300">Path</th>
                                <th class="py-2 pr-3 font-medium text-neutral-600 dark:text-neutral-300">Scope</th>
                                <th class="py-2 font-medium text-neutral-600 dark:text-neutral-300">Handler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach ($routes as $r)
                                <tr>
                                    <td class="py-2 pr-3 align-top">
                                        @foreach ($r['methods'] as $m)
                                            <x-nawasara-ui::badge :color="$methodColor($m)">{{ $m }}</x-nawasara-ui::badge>
                                        @endforeach
                                    </td>
                                    <td class="py-2 pr-3 align-top">
                                        <code class="text-xs text-neutral-800 dark:text-neutral-200">/{{ $r['uri'] }}</code>
                                    </td>
                                    <td class="py-2 pr-3 align-top">
                                        @forelse ($r['scopes'] as $s)
                                            <code class="block text-xs text-emerald-700 dark:text-emerald-400">{{ $s }}</code>
                                        @empty
                                            <span class="text-xs text-neutral-400">— (token apa pun)</span>
                                        @endforelse
                                    </td>
                                    <td class="py-2 align-top">
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $r['action'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-nawasara-ui::page.card>
        </section>
    @endforeach

    @if ($scopes)
        <section class="mt-8">
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">Daftar scope</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Scope membatasi apa yang bisa dilakukan sebuah token. Beri scope sesempit
                mungkin: token yang bocor hanya membuka apa yang benar-benar diberikan kepadanya.
            </p>

            @foreach ($scopes as $group => $list)
                <div class="mb-3">
                    <h3 class="mb-1.5 text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ $group }}</h3>
                    <x-nawasara-ui::page.card>
                        <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach ($list as $s)
                                <li class="py-2 first:pt-0 last:pb-0">
                                    <code class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ $s['name'] }}</code>
                                    <p class="mt-0.5 text-sm text-neutral-600 dark:text-neutral-300">{{ $s['description'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </x-nawasara-ui::page.card>
                </div>
            @endforeach
        </section>
    @endif
</x-nawasara-docs::shell>
