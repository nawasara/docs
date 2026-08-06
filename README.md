# Nawasara Docs

Dokumentasi internal Nawasara, hidup di dalam aplikasi di `/nawasara-docs`.

Isinya dibaca dari kode yang sedang berjalan, bukan dari catatan terpisah — daftar endpoint datang dari tabel route, daftar scope dari registry, katalog komponen dari file blade `nawasara/ui`, dan panduan dari berkas markdown yang sudah ada di repo. Dokumentasi yang menyalin akan basi tanpa ada yang menyadarinya; yang membaca dari sumbernya tidak bisa.

## Isi

| Halaman | Sumber |
|---|---|
| Katalog komponen | memindai `packages/nawasara-ui/resources/views/components/**` |
| Referensi API | `Route::getRoutes()` + `ScopeRegistry::grouped()` |
| Autentikasi & token | ditulis manual — menjelaskan alur, bukan mendaftar data |
| Install agent secscan | `packages/nawasara-secscan/README.md` mulai baris 132 |
| Membuat package baru | `CLAUDE.md` |

Halaman API juga menandai dua hal yang tidak memunculkan error di mana pun: scope yang dipakai route tapi belum didaftarkan (endpoint terkunci untuk semua token), dan scope terdaftar yang tidak dipakai route mana pun (kemungkinan middleware lupa dipasang).

## Akses

Di belakang `auth`, tanpa permission tambahan: isinya cara memakai sistem, bukan datanya. Muncul di sidebar pada grup **Pengaturan**.

## Yang perlu diketahui saat mengubah

**Katalog di-cache 5 menit.** Saat sedang menyunting komponen, panggil `POST /nawasara-docs/refresh` atau `php artisan cache:clear` supaya perubahan langsung terlihat.

**Dokumentasi komponen tidak seragam.** Sebagian punya blok `{{-- --}}` di awal file dengan bagian `Pemakaian:`, sebagian hanya komentar per-prop, sebagian tidak ada sama sekali. Halaman katalog menandai yang belum terdokumentasi alih-alih menyembunyikannya, jadi daftarnya sekaligus berfungsi sebagai daftar pekerjaan. Format paling lengkap ada di `components/badge.blade.php` — tiru itu.

**Tailwind butuh dua `@source`.** Di `resources/css/app.css` package ini terdaftar lewat `packages/` **dan** `vendor/`. Yang pertama untuk dev lokal, karena `vendor/nawasara/docs` di Windows berupa junction dan Tailwind tidak menelusurinya; yang kedua untuk build server, di mana `packages/` kosong.

**Halaman docs adalah view statis, bukan Livewire.** Komponen `x-nawasara-ui::form.*` mengandalkan konteks Livewire dan akan melempar `Using $this when not in object context` bila dipakai di sini — pakai elemen native untuk kebutuhan sederhana seperti kotak pencarian sisi klien.

## Menambah halaman

1. Buat blade di `resources/views/pages/`
2. Daftarkan rute di `routes/web.php` dalam grup `['web', 'auth']`
3. Tambahkan ke `src/Support/DocsNavigation.php` — sidebar docs dan halaman indeks membacanya dari sana, jadi cukup satu tempat
4. Kalau perlu muncul di sidebar aplikasi, tambahkan juga ke `config/menu.php`

## Author

**Pringgo J. Saputro** &lt;odyinggo@gmail.com&gt;

## License

MIT
