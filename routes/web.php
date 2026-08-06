<?php

use Illuminate\Support\Facades\Route;
use Nawasara\Docs\Support\ComponentCatalog;
use Nawasara\Docs\Support\GuideRenderer;

/*
|--------------------------------------------------------------------------
| Nawasara Docs
|--------------------------------------------------------------------------
| Dokumentasi internal, di belakang login. Isinya menyebut nama host, contoh
| konfigurasi, dan cara kerja bagian dalam sistem — bukan bahan untuk pembaca
| anonim, jadi seluruh grup memakai middleware `auth`.
|
| Tidak digerbang permission tambahan: siapa pun yang sudah punya akun boleh
| membaca cara memakai komponen dan API. Yang perlu dijaga adalah datanya,
| dan itu sudah dijaga di endpoint masing-masing.
*/

Route::middleware(['web', 'auth'])->prefix('nawasara-docs')->group(function () {

    Route::view('/', 'nawasara-docs::pages.index')
        ->name('nawasara-docs.index');

    // ── Komponen ────────────────────────────────────────────
    Route::view('/components', 'nawasara-docs::pages.components.index')
        ->name('nawasara-docs.components.index');

    Route::view('/components/gallery', 'nawasara-docs::pages.examples.base')
        ->name('nawasara-docs.components.gallery');

    // Halaman contoh lama — dipertahankan supaya tautan yang sudah beredar
    // tidak mati, tapi tidak lagi muncul di navigasi.
    Route::view('/components/table', 'nawasara-docs::pages.examples.table')
        ->name('nawasara-docs.components.table');

    Route::view('/components/form', 'nawasara-docs::pages.examples.form')
        ->name('nawasara-docs.components.form');

    // ── API ─────────────────────────────────────────────────
    Route::view('/api', 'nawasara-docs::pages.api.index')
        ->name('nawasara-docs.api.index');

    Route::view('/api/auth', 'nawasara-docs::pages.api.auth')
        ->name('nawasara-docs.api.auth');

    // ── Panduan ─────────────────────────────────────────────
    // Merender berkas markdown yang sudah ada di repo. Rentang barisnya
    // ditunjuk eksplisit karena README secscan sebenarnya memuat dua dokumen:
    // dokumentasi package (atas) dan panduan install agent (bawah, mulai H1
    // kedua). Yang ditampilkan di sini hanya bagian panduannya.
    Route::get('/guides/install-agent', function (GuideRenderer $renderer) {
        return view('nawasara-docs::pages.guide', [
            'title' => 'Install Agent Secscan',
            'lead' => 'Memasang nawasara-agent di server OPD agar insiden keamanannya terpantau.',
            'source' => 'packages/nawasara-secscan/README.md',
            'guide' => $renderer->render('packages/nawasara-secscan/README.md', 132),
            'breadcrumb' => [['label' => 'Panduan'], ['label' => 'Install Agent']],
        ]);
    })->name('nawasara-docs.guides.agent');

    Route::get('/guides/package', function (GuideRenderer $renderer) {
        return view('nawasara-docs::pages.guide', [
            'title' => 'Membuat Package Baru',
            'lead' => 'Konvensi scaffold dan wiring yang wajib. Melewatkan salah satunya biasanya tidak memunculkan error — menu tidak muncul, atau kelas Tailwind tidak ter-compile, tanpa keluhan apa pun.',
            'source' => 'CLAUDE.md',
            'guide' => $renderer->render('CLAUDE.md'),
            'breadcrumb' => [['label' => 'Panduan'], ['label' => 'Membuat Package']],
        ]);
    })->name('nawasara-docs.guides.package');

    // ── Muat ulang katalog ──────────────────────────────────
    // Katalog komponen di-cache; tombol ini untuk melihat perubahan blade
    // tanpa menunggu cache kedaluwarsa saat sedang mengerjakan komponen.
    Route::post('/refresh', function (ComponentCatalog $components, GuideRenderer $guides) {
        $components->forget();
        $guides->forget();

        return back()->with('status', 'Katalog dimuat ulang.');
    })->name('nawasara-docs.refresh');
});
