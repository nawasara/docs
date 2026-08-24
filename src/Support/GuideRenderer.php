<?php

namespace Nawasara\Docs\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Merender panduan yang sumbernya file markdown di repo (README package,
 * CLAUDE.md) menjadi halaman docs, lengkap dengan daftar isi.
 *
 * Menampilkan file yang sudah ada, bukan salinannya, supaya panduan install
 * agent hanya perlu diperbarui di satu tempat. Dokumentasi operasional yang
 * bercabang dua lebih berbahaya daripada yang tidak lengkap — pembaca tidak
 * punya cara tahu salinan mana yang masih benar.
 */
class GuideRenderer
{
    protected const CACHE_TTL = 300;

    /**
     * Render sebuah file markdown (atau potongannya) menjadi HTML + daftar isi.
     *
     * @param  string  $path      relatif terhadap root proyek
     * @param  ?int    $fromLine  1-indexed, inklusif; null = dari awal
     * @param  ?int    $toLine    1-indexed, inklusif; null = sampai akhir
     * @return array{html:string, toc:array<int,array{level:int,text:string,anchor:string}>, missing:bool}
     */
    public function render(string $path, ?int $fromLine = null, ?int $toLine = null): array
    {
        $key = 'nawasara-docs.guide.'.md5($path.'|'.$fromLine.'|'.$toLine);

        return Cache::remember($key, self::CACHE_TTL, function () use ($path, $fromLine, $toLine) {
            $full = $this->locate($path);

            if ($full === null) {
                return ['html' => '', 'toc' => [], 'missing' => true];
            }

            $markdown = $this->slice((string) file_get_contents($full), $fromLine, $toLine);

            return [
                'html' => $this->toHtml($markdown),
                'toc' => $this->tableOfContents($markdown),
                'missing' => false,
            ];
        });
    }

    /**
     * Mencari berkas panduan, karena letaknya BERBEDA antara pengembangan
     * dan produksi.
     *
     * Di repo pengembangan paket ada di `packages/nawasara-x/`. Di produksi
     * folder itu tidak ikut ke dalam image sama sekali — Composer memasang
     * paketnya dari Packagist ke `vendor/nawasara/x/`. Jalur yang ditulis
     * pemanggil karena itu hanya benar di satu sisi, dan halamannya tampil
     * kosong di sisi yang lain: rute ada, menu ada, isi tidak.
     *
     * Ditemukan 24 Agustus 2026 — panduan install agent secscan kosong di
     * produksi sejak dirilis, tanpa satu pun galat tercatat.
     *
     * @return string|null  Jalur mutlak, atau null bila tidak ditemukan.
     */
    protected function locate(string $path): ?string
    {
        $candidates = [$path];

        // packages/nawasara-secscan/README.md → vendor/nawasara/secscan/README.md
        if (preg_match('#^packages/nawasara-([^/]+)/(.+)$#', $path, $m)) {
            $candidates[] = "vendor/nawasara/{$m[1]}/{$m[2]}";
        }

        foreach ($candidates as $candidate) {
            $full = base_path($candidate);

            if (is_file($full)) {
                return $full;
            }
        }

        return null;
    }

    public function forget(): void
    {
        // Cache::flush() terlalu luas — cukup tandai ulang lewat TTL pendek.
        // Method ini disediakan supaya pemanggil punya titik masuk eksplisit.
        Cache::forget('nawasara-docs.guide');
    }

    /** Ambil rentang baris tertentu, mempertahankan penomoran 1-indexed. */
    protected function slice(string $content, ?int $from, ?int $to): string
    {
        if ($from === null && $to === null) {
            return $content;
        }

        $lines = preg_split('/\r?\n/', $content);
        $start = max(0, ($from ?? 1) - 1);
        $length = $to !== null ? max(0, $to - $start) : null;

        return implode("\n", array_slice($lines, $start, $length));
    }

    protected function toHtml(string $markdown): string
    {
        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'heading_permalink' => ['id_prefix' => ''],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $html = (string) (new MarkdownConverter($environment))->convert($markdown);

        return $this->addHeadingAnchors($html);
    }

    /**
     * Beri id pada heading supaya daftar isi bisa menautinya.
     *
     * Anchor dihitung dengan aturan yang sama seperti tableOfContents(), jadi
     * keduanya selalu cocok tanpa perlu saling mengoper state.
     */
    protected function addHeadingAnchors(string $html): string
    {
        $seen = [];

        return (string) preg_replace_callback(
            '/<h([1-6])>(.*?)<\/h\1>/s',
            function (array $m) use (&$seen) {
                $text = trim(strip_tags($m[2]));
                $anchor = $this->anchor($text, $seen);

                return '<h'.$m[1].' id="'.e($anchor).'">'.$m[2].'</h'.$m[1].'>';
            },
            $html,
        );
    }

    /**
     * Daftar isi dari heading markdown.
     *
     * Fenced code block dibuang lebih dulu: baris seperti `# 1. Cek status`
     * di dalam contoh shell bukan heading, dan parser naif akan
     * memasukkannya ke daftar isi sebagai bagian dokumen.
     *
     * @return array<int, array{level:int, text:string, anchor:string}>
     */
    protected function tableOfContents(string $markdown): array
    {
        $withoutCode = (string) preg_replace('/^```.*?^```/ms', '', $markdown);

        if (! preg_match_all('/^(#{1,3})\s+(.+?)\s*$/m', $withoutCode, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $seen = [];
        $toc = [];

        foreach ($matches as $m) {
            $text = trim($m[2]);

            $toc[] = [
                'level' => strlen($m[1]),
                'text' => $text,
                'anchor' => $this->anchor($text, $seen),
            ];
        }

        return $toc;
    }

    /**
     * Anchor unik dari teks heading. Judul yang sama muncul dua kali di satu
     * dokumen akan mendapat akhiran angka, supaya tautan tidak saling rebut.
     *
     * @param  array<string,int>  $seen
     */
    protected function anchor(string $text, array &$seen): string
    {
        $base = Str::slug($text) ?: 'bagian';
        $seen[$base] = ($seen[$base] ?? 0) + 1;

        return $seen[$base] === 1 ? $base : $base.'-'.$seen[$base];
    }
}
