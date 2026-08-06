<?php

namespace Nawasara\Docs\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Katalog endpoint API, dibaca dari tabel route Laravel dan registry scope
 * `nawasara/api` saat request berjalan.
 *
 * Tidak ada daftar yang ditulis tangan di sini. Endpoint yang ditambahkan
 * sebuah package akan muncul begitu route-nya terdaftar, dan endpoint yang
 * dihapus akan hilang dengan sendirinya. Itu penting untuk halaman yang
 * dipakai orang sebagai rujukan: dokumentasi API yang salah lebih merugikan
 * daripada tidak ada dokumentasi sama sekali.
 *
 * Scope diambil dari middleware `scope:` pada tiap route, bukan dari catatan
 * terpisah — jadi yang tertulis di docs persis yang ditegakkan server.
 */
class ApiCatalog
{
    /**
     * Endpoint dikelompokkan per domain (segmen setelah prefix api/v1).
     *
     * @return array<string, array<int, array{
     *   methods:array<int,string>, uri:string, name:?string,
     *   scopes:array<int,string>, domain:string, action:string
     * }>>
     */
    public function grouped(): array
    {
        $out = [];

        foreach ($this->all() as $route) {
            $out[$route['domain']][] = $route;
        }

        ksort($out);

        return $out;
    }

    /** @return array<int, array<string,mixed>> */
    public function all(): array
    {
        $prefix = trim((string) config('nawasara-api.route.prefix', 'api/v1'), '/');

        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if (! str_starts_with($uri, $prefix.'/') && $uri !== $prefix) {
                continue;
            }

            // HEAD selalu menyertai GET di tabel route Laravel; menampilkannya
            // hanya menambah derau bagi pembaca.
            $methods = array_values(array_diff($route->methods(), ['HEAD']));

            if ($methods === []) {
                continue;
            }

            $routes[] = [
                'methods' => $methods,
                'uri' => $uri,
                'name' => $route->getName(),
                'scopes' => $this->scopesFor($route),
                'domain' => $this->domainFor($uri, $prefix),
                'action' => $this->actionFor($route),
            ];
        }

        usort($routes, fn ($a, $b) => [$a['domain'], $a['uri']] <=> [$b['domain'], $b['uri']]);

        return $routes;
    }

    /**
     * Seluruh scope terdaftar, dikelompokkan per package.
     *
     * @return array<string, array<int, array{name:string, description:string, group:string}>>
     */
    public function scopes(): array
    {
        $registry = $this->scopeRegistry();

        return $registry ? $registry->grouped() : [];
    }

    /**
     * Scope yang terdaftar tapi tidak dipakai route mana pun, dan sebaliknya.
     *
     * Ditampilkan di docs karena keduanya menandakan kekeliruan yang tidak
     * memunculkan error: scope yatim berarti ada yang lupa memasang middleware,
     * sedangkan scope tak terdaftar berarti UI token tidak bisa memberikannya
     * ke siapa pun — endpoint-nya terkunci untuk semua orang.
     *
     * @return array{unused:array<int,string>, unregistered:array<int,string>}
     */
    public function scopeMismatches(): array
    {
        $registered = [];
        foreach ($this->scopes() as $group) {
            foreach ($group as $s) {
                $registered[] = $s['name'];
            }
        }

        $used = [];
        foreach ($this->all() as $r) {
            foreach ($r['scopes'] as $s) {
                $used[] = $s;
            }
        }

        $used = array_values(array_unique($used));

        return [
            'unused' => array_values(array_diff($registered, $used, $this->enforcedInResource())),
            'unregistered' => array_values(array_diff($used, $registered)),
        ];
    }

    /**
     * Scope yang sengaja TIDAK dipasang sebagai middleware karena ditegakkan
     * di dalam Resource — bukan menentukan boleh-tidaknya memanggil endpoint,
     * melainkan seberapa banyak yang dikembalikan.
     *
     * Tanpa daftar ini, scope semacam itu akan selamanya tampil sebagai
     * "terdaftar tapi tak dipakai", yaitu peringatan yang tidak bisa
     * ditindaklanjuti — dan peringatan yang selalu menyala akan diabaikan
     * saat suatu hari ia benar-benar menandakan masalah.
     *
     * Dideteksi dari deskripsinya: scope yang menyebut dirinya "tambahan atas"
     * scope lain memang bekerja seperti ini. Itu membuat daftarnya tidak perlu
     * diperbarui manual setiap kali pola ini dipakai lagi.
     *
     * @return array<int, string>
     */
    protected function enforcedInResource(): array
    {
        $out = [];

        foreach ($this->scopes() as $group) {
            foreach ($group as $s) {
                if (stripos($s['description'], 'tambahan atas') !== false) {
                    $out[] = $s['name'];
                }
            }
        }

        return $out;
    }

    public function stats(): array
    {
        $all = $this->all();
        $scopeCount = 0;
        foreach ($this->scopes() as $group) {
            $scopeCount += count($group);
        }

        return [
            'endpoints' => count($all),
            'domains' => count($this->grouped()),
            'scopes' => $scopeCount,
        ];
    }

    /**
     * Scope yang dibutuhkan sebuah route.
     *
     * Beberapa scope pada satu route bersifat DAN, bukan ATAU — token harus
     * memiliki semuanya (lihat RequireScope).
     *
     * @return array<int, string>
     */
    protected function scopesFor($route): array
    {
        $out = [];

        foreach ($route->gatherMiddleware() as $middleware) {
            if (! is_string($middleware) || ! str_starts_with($middleware, 'scope:')) {
                continue;
            }

            foreach (explode(',', substr($middleware, 6)) as $scope) {
                $scope = trim($scope);
                if ($scope !== '') {
                    $out[] = $scope;
                }
            }
        }

        return array_values(array_unique($out));
    }

    /** Segmen domain setelah prefix: api/v1/secscan/stats → 'secscan'. */
    protected function domainFor(string $uri, string $prefix): string
    {
        $rest = trim(Str::after($uri, $prefix), '/');
        $first = Str::before($rest, '/');

        // Endpoint meta (api/v1/me, api/v1/scopes) tidak punya segmen domain.
        return ($first === '' || ! str_contains($rest, '/')) ? 'meta' : $first;
    }

    protected function actionFor($route): string
    {
        $action = $route->getActionName();

        if ($action === 'Closure') {
            return 'Closure';
        }

        return Str::afterLast($action, '\\');
    }

    protected function scopeRegistry(): ?object
    {
        if (! class_exists(\Nawasara\Api\Support\ScopeRegistry::class)) {
            return null;
        }

        try {
            return app(\Nawasara\Api\Support\ScopeRegistry::class);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
