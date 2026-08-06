<?php

namespace Nawasara\Docs\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

/**
 * Membaca katalog komponen langsung dari file blade `nawasara/ui`.
 *
 * Dibaca dari sumbernya, bukan dari daftar yang ditulis tangan, karena daftar
 * tangan akan basi diam-diam: komponen baru ditambahkan tanpa ada yang ingat
 * memperbarui docs, dan halaman ini justru paling dipercaya saat paling salah.
 *
 * Dokumentasi di komponen tidak seragam — sebagian punya blok komentar di awal
 * file, sebagian hanya komentar per-prop di dalam @props, sebagian tidak ada
 * sama sekali. Parser ini mengambil apa yang ada dan jujur menandai yang belum
 * terdokumentasi, sehingga halaman docs sekaligus berfungsi sebagai daftar
 * pekerjaan.
 */
class ComponentCatalog
{
    /** Umur cache. Pendek di lokal supaya edit komponen langsung terlihat. */
    protected const CACHE_TTL = 300;

    protected const CACHE_KEY = 'nawasara-docs.component-catalog';

    /**
     * Seluruh komponen, dikelompokkan per folder ('root', 'form', 'page', …).
     *
     * @return array<string, array<int, array{
     *   name:string, tag:string, group:string, path:string,
     *   description:?string, usage:?string, props:array<int,array{name:string,default:?string,comment:?string}>,
     *   documented:bool
     * }>>
     */
    public function grouped(): array
    {
        $components = $this->all();

        $out = [];
        foreach ($components as $c) {
            $out[$c['group']][] = $c;
        }

        // 'root' didahulukan — itu komponen yang paling sering dipakai;
        // sisanya (form, page, layouts) menyusul secara alfabet.
        uksort($out, function (string $a, string $b) {
            if ($a === 'root') return -1;
            if ($b === 'root') return 1;

            return strcmp($a, $b);
        });

        return $out;
    }

    /** @return array<int, array<string,mixed>> */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $base = $this->componentPath();

            if (! $base || ! is_dir($base)) {
                return [];
            }

            $finder = (new Finder())->files()->in($base)->name('*.blade.php')->sortByName();

            $out = [];
            foreach ($finder as $file) {
                $parsed = $this->parse($file->getContents());
                $relative = str_replace('\\', '/', $file->getRelativePathname());
                $name = Str::beforeLast($relative, '.blade.php');

                // layouts/* adalah kerangka halaman, bukan komponen yang
                // disisipkan ke dalam halaman — memasukkannya ke galeri akan
                // menyesatkan, karena tidak bisa dipreview seperti yang lain.
                if (str_starts_with($name, 'layouts/')) {
                    continue;
                }

                $group = str_contains($name, '/') ? Str::before($name, '/') : 'root';

                $out[] = [
                    'name' => $name,
                    'tag' => 'x-nawasara-ui::'.str_replace('/', '.', $name),
                    'group' => $group,
                    'path' => 'packages/nawasara-ui/resources/views/components/'.$relative,
                    'description' => $parsed['description'],
                    'usage' => $parsed['usage'],
                    'props' => $parsed['props'],
                    'documented' => $parsed['description'] !== null || $parsed['usage'] !== null,
                ];
            }

            return $out;
        });
    }

    public function find(string $name): ?array
    {
        foreach ($this->all() as $c) {
            if ($c['name'] === $name) {
                return $c;
            }
        }

        return null;
    }

    /** Ringkasan untuk halaman indeks. */
    public function stats(): array
    {
        $all = $this->all();

        return [
            'total' => count($all),
            'documented' => count(array_filter($all, fn ($c) => $c['documented'])),
            'groups' => count($this->grouped()),
        ];
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil deskripsi, contoh pemakaian, dan daftar prop dari isi file blade.
     *
     * @return array{description:?string, usage:?string, props:array<int,array{name:string,default:?string,comment:?string}>}
     */
    protected function parse(string $source): array
    {
        return [
            'description' => $this->extractDescription($source),
            'usage' => $this->extractUsage($source),
            'props' => $this->extractProps($source),
        ];
    }

    /**
     * Blok `{{-- … --}}` di awal file, sampai sebelum baris "Pemakaian:".
     *
     * Hanya blok pembuka yang dibaca; komentar di tengah file adalah catatan
     * implementasi untuk yang mengedit komponen, bukan untuk yang memakainya.
     */
    protected function extractDescription(string $source): ?string
    {
        if (! preg_match('/^\s*\{\{--(.*?)--\}\}/s', $source, $m)) {
            return null;
        }

        $block = trim($m[1]);

        // Potong di penanda contoh pemakaian — bagian itu diambil terpisah.
        $block = preg_split('/^\s*(Pemakaian|Usage|Contoh)\s*:?/mi', $block)[0];

        $text = trim(preg_replace('/\n\s*\n\s*\n+/', "\n\n", $block));

        return $text === '' ? null : $text;
    }

    /**
     * Cuplikan contoh dari bagian "Pemakaian:" di blok komentar pembuka.
     */
    protected function extractUsage(string $source): ?string
    {
        if (! preg_match('/^\s*\{\{--(.*?)--\}\}/s', $source, $m)) {
            return null;
        }

        if (! preg_match('/^\s*(?:Pemakaian|Usage|Contoh)[^\n]*:?\s*\n(.*?)(?:\n\s*\n\s*\S|$)/msi', $m[1], $u)) {
            return null;
        }

        $snippet = rtrim($u[1]);

        // Buang indentasi bersama supaya cuplikan bisa langsung disalin.
        $lines = preg_split('/\r?\n/', $snippet);
        $indents = [];
        foreach ($lines as $line) {
            if (trim($line) !== '' && preg_match('/^(\s*)/', $line, $i)) {
                $indents[] = strlen($i[1]);
            }
        }
        $strip = $indents ? min($indents) : 0;

        $out = implode("\n", array_map(
            fn ($l) => substr($l, min($strip, strlen($l) - strlen(ltrim($l)))),
            $lines,
        ));

        $out = trim($out);

        return $out === '' ? null : $out;
    }

    /**
     * Daftar prop dari `@props([...])`, beserta nilai default dan komentar
     * penjelas yang menyertainya (baik `//` di belakang maupun PHPDoc di atas).
     *
     * @return array<int, array{name:string, default:?string, comment:?string}>
     */
    protected function extractProps(string $source): array
    {
        if (! preg_match('/@props\(\s*\[(.*?)\]\s*\)/s', $source, $m)) {
            return [];
        }

        $body = $m[1];
        $props = [];
        $pendingDoc = null;

        foreach (preg_split('/\r?\n/', $body) as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            // PHPDoc di atas prop — kumpulkan sampai ketemu barisnya.
            if (str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $clean = trim(preg_replace('#^/?\*+/?#', '', $trimmed), " \t*/");
                if ($clean !== '') {
                    $pendingDoc = $pendingDoc ? $pendingDoc.' '.$clean : $clean;
                }
                continue;
            }

            // 'name' => default,   // komentar
            // atau: 'name',        (prop tanpa default)
            if (! preg_match("/^'([^']+)'\s*(?:=>\s*(.+?))?\s*,?\s*(?:\/\/\s*(.*))?$/", $trimmed, $p)) {
                continue;
            }

            $default = isset($p[2]) ? trim(rtrim($p[2], ','), " \t") : null;
            $inline = isset($p[3]) ? trim($p[3]) : null;

            // Komentar `//` bisa tertangkap sebagai bagian default kalau
            // regex-nya rakus; rapikan kalau itu terjadi.
            if ($default !== null && str_contains($default, '//')) {
                [$default, $tail] = array_map('trim', explode('//', $default, 2));
                $default = rtrim($default, ',');
                $inline = $inline ?: $tail;
            }

            $props[] = [
                'name' => $p[1],
                'default' => ($default === '' || $default === null) ? null : $default,
                'comment' => $pendingDoc ?: $inline,
            ];

            $pendingDoc = null;
        }

        return $props;
    }

    /**
     * Lokasi folder komponen. Di monorepo ini `vendor/nawasara/ui` adalah
     * symlink ke `packages/nawasara-ui`, jadi keduanya menunjuk file yang sama;
     * `packages/` dicoba lebih dulu supaya tetap bekerja bila symlink tidak ada.
     */
    protected function componentPath(): ?string
    {
        foreach ([
            base_path('packages/nawasara-ui/resources/views/components'),
            base_path('vendor/nawasara/ui/resources/views/components'),
        ] as $candidate) {
            if (is_dir($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
