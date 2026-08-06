<x-nawasara-docs::shell
    title="Menambah Endpoint API"
    :breadcrumb="[['label' => 'Panduan'], ['label' => 'Menambah endpoint API']]"
    lead="Menempelkan endpoint dan scope baru ke package yang sudah ada. Empat berkas, dan satu urutan yang tidak boleh terbalik.">

    <div class="space-y-5">

        <div class="rounded-lg border border-sky-200 bg-sky-50 p-3 dark:border-sky-800 dark:bg-sky-900/20">
            <p class="text-sm text-sky-900 dark:text-sky-100">
                API Nawasara <strong>bukan</strong> Sanctum. Ada kerangka token dan scope tersendiri di
                <code class="text-xs">nawasara/api</code>: package itu menyediakan token, registry scope,
                middleware, dan audit log; package domain menyimpan route, controller, dan resource-nya
                sendiri, lalu menempel lewat <code class="text-xs">class_exists()</code> tanpa dependency composer.
            </p>
        </div>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">1. Resource — putuskan apa yang keluar</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Mulai dari sini, bukan dari route. Resource adalah <strong>allow-list</strong>, bukan dump yang
                disaring: sebutkan field satu per satu, dan tulis alasan pemblokiran di PHPDoc kelasnya.
                Field yang lupa disebut tidak akan keluar — itu arah gagal yang benar.
            </p>

            <x-nawasara-docs::code lang="php" label="src/Http/Resources/FooResource.php">@php echo <<<'CODE'
<?php

namespace Nawasara\Contoh\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Yang sengaja DIBLOK dan alasannya:
 *   - `internal_notes` — catatan operator bebas isi; tidak ada jaminan aman.
 *   - `api_key_hash`   — kredensial, tidak pernah keluar ke mana pun.
 */
class FooResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->public_slug,
            'name'       => $this->name,
            'status'     => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
CODE; @endphp</x-nawasara-docs::code>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">2. Controller</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Extends <code class="text-xs">Illuminate\Routing\Controller</code>, kembalikan
                <code class="text-xs">data</code> + <code class="text-xs">meta</code>. Paginasi butuh
                kolom unik sebagai tie-breaker — tanpa itu, baris bisa berpindah halaman di tengah
                penelusuran saat data berubah.
            </p>

            <x-nawasara-docs::code lang="php" label="src/Http/Api/FooController.php">@php echo <<<'CODE'
public function index(Request $request): JsonResponse
{
    $query = Foo::query();

    if ($q = trim((string) $request->query('q', ''))) {
        $query->search($q);
    }

    $perPage = min(100, max(1, (int) $request->query('per_page', 50)));

    $rows = $query->orderByDesc('created_at')->orderByDesc('id')->paginate($perPage);

    return response()->json([
        'data' => FooResource::collection($rows->items())->resolve(),
        'meta' => [
            'total'        => $rows->total(),
            'per_page'     => $rows->perPage(),
            'current_page' => $rows->currentPage(),
            'last_page'    => $rows->lastPage(),
        ],
    ]);
}
CODE; @endphp</x-nawasara-docs::code>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">3. Route</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Scope dipasang per-route, autentikasi di grup. Beberapa scope pada satu route
                bersifat <strong>DAN</strong>, bukan ATAU — token harus punya semuanya.
            </p>

            <x-nawasara-docs::code lang="php" label="routes/api.php">@php echo <<<'CODE'
Route::middleware('scope:contoh.foo.read')->group(function () {
    Route::get('/foos', [FooController::class, 'index'])->name('foos.index');

    // Route statis HARUS didaftarkan sebelum {param}, kalau tidak
    // "by-name" akan tertangkap sebagai {id} dan selalu 404.
    Route::get('/foos/by-name/{name}', [FooController::class, 'showByName'])->name('foos.show-by-name');
    Route::get('/foos/{id}', [FooController::class, 'show'])->whereNumber('id')->name('foos.show');
});
CODE; @endphp</x-nawasara-docs::code>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                Nama route jangan mengulang nama domain. Prefix <code class="text-xs">nawasara-api.contoh.</code>
                sudah ditambahkan saat mount, jadi <code class="text-xs">'contoh.foos.index'</code> akan
                menghasilkan <code class="text-xs">nawasara-api.contoh.contoh.foos.index</code>.
            </p>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">4. ServiceProvider</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Scope didaftarkan <strong>sebelum</strong> route. UI token memfilter scope yang tidak
                ter-register, jadi lupa mendaftarkan berarti scope itu tidak bisa diberikan ke token
                mana pun — endpoint-nya terkunci untuk semua orang, tanpa error di mana pun.
            </p>

            <x-nawasara-docs::code lang="php" label="src/ContohServiceProvider.php">@php echo <<<'CODE'
public function boot(): void
{
    // ... loadRoutesFrom, loadViewsFrom, dst.

    $this->registerApiScopes();   // urutan ini penting
    $this->registerApiRoutes();
}

public function registerApiScopes(): void
{
    if (! class_exists(\Nawasara\Api\Support\ScopeRegistry::class)) {
        return;
    }

    $this->app->make(\Nawasara\Api\Support\ScopeRegistry::class)->register(
        'contoh.foo.read',
        'Deskripsi yang muncul di UI token. Sebutkan apa yang TIDAK termasuk.',
    );
}

public function registerApiRoutes(): void
{
    if (! class_exists(\Nawasara\Api\ApiServiceProvider::class)) {
        return;
    }

    $prefix = (string) config('nawasara-api.route.prefix', 'api/v1').'/contoh';

    \Illuminate\Support\Facades\Route::prefix($prefix)
        ->middleware(['api', 'api.auth', 'api.log'])
        ->name('nawasara-api.contoh.')
        ->group(__DIR__.'/../routes/api.php');
}
CODE; @endphp</x-nawasara-docs::code>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                Guard <code class="text-xs">class_exists</code>, <strong>jangan</strong> tambahkan
                <code class="text-xs">nawasara/api</code> ke composer require — package tetap jalan
                penuh tanpanya, hanya API-nya yang absen.
            </p>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">Scope yang mengatur isi, bukan akses</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Kadang sebuah scope tidak menentukan boleh-tidaknya memanggil endpoint, melainkan
                seberapa banyak yang dikembalikan — seperti <code class="text-xs">zoom.meeting.join</code>
                yang membuka tautan masuk rapat. Scope semacam itu dicek di dalam Resource, bukan
                dipasang sebagai middleware.
            </p>

            <x-nawasara-docs::code lang="php">@php echo <<<'CODE'
protected function tokenCanJoin(Request $request): bool
{
    $token = $request->attributes->get('api_token');

    if (! $token || ! method_exists($token, 'scopeNames')) {
        return false;   // gagal ke sisi tertutup
    }

    return in_array('zoom.meeting.join', $token->scopeNames(), true);
}
CODE; @endphp</x-nawasara-docs::code>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                Tulis <em>&ldquo;tambahan atas&rdquo;</em> di awal deskripsi scope-nya. Halaman
                <a href="{{ route('nawasara-docs.api.index') }}" wire:navigate class="text-emerald-600 hover:underline dark:text-emerald-400">Referensi API</a>
                memakai frasa itu untuk mengenali scope yang sengaja tidak jadi middleware, supaya ia
                tidak selamanya dilaporkan sebagai &ldquo;terdaftar tapi tak dipakai&rdquo;.
            </p>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">Sebelum menyebut selesai</h2>
            <ul class="space-y-1.5 text-sm text-neutral-600 dark:text-neutral-300">
                <li class="flex gap-2"><span class="text-neutral-400">1.</span> <span><code class="text-xs">php artisan route:list --path=api/v1</code> — route terdaftar dan scope-nya terbaca.</span></li>
                <li class="flex gap-2"><span class="text-neutral-400">2.</span> <span>Buka <a href="{{ route('nawasara-docs.api.index') }}" wire:navigate class="text-emerald-600 hover:underline dark:text-emerald-400">Referensi API</a> — endpoint baru muncul sendiri, dan tidak ada peringatan ketidakcocokan scope.</span></li>
                <li class="flex gap-2"><span class="text-neutral-400">3.</span> <span>Uji kebocoran dengan <strong>data nyata</strong>. Tabel yang kosong di dev membuat pengecekan allow-list terlewat diam-diam — buat baris uji berisi penanda, lalu cari penanda itu di seluruh body respons, bukan sekadar cek nama key.</span></li>
                <li class="flex gap-2"><span class="text-neutral-400">4.</span> <span>Uji token tanpa scope → harus <code class="text-xs">403 insufficient_scope</code>, dan tanpa token → <code class="text-xs">401</code>.</span></li>
                <li class="flex gap-2"><span class="text-neutral-400">5.</span> <span>Perbarui README package dan tambahkan penjelasan domainnya di <code class="text-xs">ApiExamples</code>.</span></li>
            </ul>
        </x-nawasara-ui::page.card>

    </div>
</x-nawasara-docs::shell>
