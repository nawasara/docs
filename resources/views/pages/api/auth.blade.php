@php
    $base = rtrim(config('app.url'), '/');
    $prefix = trim((string) config('nawasara-api.route.prefix', 'api/v1'), '/');
@endphp

<x-nawasara-docs::shell
    title="Autentikasi & Token API"
    :breadcrumb="[['label' => 'API', 'url' => route('nawasara-docs.api.index')], ['label' => 'Autentikasi']]"
    lead="Setiap endpoint API butuh token. Halaman ini menjelaskan cara membuatnya, memakainya, dan membatasi jangkauannya.">

    <div class="space-y-5">

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">1. Membuat token</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Buka <strong>Pengaturan → API Token</strong>, lalu pilih scope yang dibutuhkan.
                Token ditampilkan <strong>satu kali saja</strong> saat dibuat — setelah itu hanya
                delapan karakter awalnya yang bisa dilihat, karena yang disimpan server adalah
                hash-nya, bukan tokennya.
            </p>
            <p class="text-sm text-neutral-600 dark:text-neutral-300">
                Kalau token hilang, tidak ada cara memulihkannya; buat yang baru dan cabut yang lama.
            </p>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">2. Mengirim token</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Dua cara, keduanya diterima. Pakai <code>Authorization</code> kalau tidak ada alasan khusus.
            </p>

            <div class="space-y-3">
                <x-nawasara-docs::code lang="bash" label="Authorization header">curl -H "Authorization: Bearer nws_xxxxxxxx" \
  "{{ $base }}/{{ $prefix }}/keycloak/users?q=budi"</x-nawasara-docs::code>

                <x-nawasara-docs::code lang="bash" label="X-API-Key header">curl -H "X-API-Key: nws_xxxxxxxx" \
  "{{ $base }}/{{ $prefix }}/secscan/stats?days=7"</x-nawasara-docs::code>
            </div>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">3. Bentuk respons</h2>
            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Endpoint daftar mengembalikan <code>data</code> dan <code>meta</code>.
                Endpoint detail hanya <code>data</code>.
            </p>

            <x-nawasara-docs::code lang="json">{
  "data": [ { "...": "..." } ],
  "meta": { "total": 120, "per_page": 50, "current_page": 1, "last_page": 3 }
}</x-nawasara-docs::code>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">4. Kode kesalahan</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-200 text-left dark:border-neutral-700">
                            <th class="py-2 pr-4 font-medium text-neutral-600 dark:text-neutral-300">HTTP</th>
                            <th class="py-2 pr-4 font-medium text-neutral-600 dark:text-neutral-300">Kode</th>
                            <th class="py-2 font-medium text-neutral-600 dark:text-neutral-300">Artinya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        <tr>
                            <td class="py-2 pr-4 align-top"><x-nawasara-ui::badge color="warning">401</x-nawasara-ui::badge></td>
                            <td class="py-2 pr-4 align-top"><code class="text-xs">missing_token</code></td>
                            <td class="py-2 align-top text-neutral-600 dark:text-neutral-300">Header token tidak dikirim.</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top"><x-nawasara-ui::badge color="warning">401</x-nawasara-ui::badge></td>
                            <td class="py-2 pr-4 align-top"><code class="text-xs">invalid_token</code></td>
                            <td class="py-2 align-top text-neutral-600 dark:text-neutral-300">Token tidak dikenal, sudah dicabut, atau kedaluwarsa.</td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top"><x-nawasara-ui::badge color="danger">403</x-nawasara-ui::badge></td>
                            <td class="py-2 pr-4 align-top"><code class="text-xs">insufficient_scope</code></td>
                            <td class="py-2 align-top text-neutral-600 dark:text-neutral-300">
                                Token sah tapi tidak punya scope yang dibutuhkan. Respons menyebut
                                scope mana yang kurang.
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 pr-4 align-top"><x-nawasara-ui::badge color="danger">403</x-nawasara-ui::badge></td>
                            <td class="py-2 pr-4 align-top"><code class="text-xs">ip_not_allowed</code></td>
                            <td class="py-2 align-top text-neutral-600 dark:text-neutral-300">IP pemanggil di luar daftar yang diizinkan token.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <x-nawasara-docs::code lang="json" label="Contoh 403">{
  "error": {
    "code": "insufficient_scope",
    "message": "Token tidak punya scope yang dibutuhkan.",
    "required": ["secscan.incident.read"],
    "missing": ["secscan.incident.read"]
  }
}</x-nawasara-docs::code>
            </div>
        </x-nawasara-ui::page.card>

        <x-nawasara-ui::page.card>
            <h2 class="mb-2 text-base font-semibold text-neutral-900 dark:text-neutral-50">5. Membatasi jangkauan token</h2>

            <p class="mb-3 text-sm text-neutral-600 dark:text-neutral-300">
                Tiga lapis, dan ketiganya berdiri sendiri:
            </p>

            <ul class="mb-3 space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
                <li>
                    <strong class="text-neutral-800 dark:text-neutral-100">Scope</strong> — pertahanan
                    terkuat. Token untuk dasbor cukup diberi scope statistik; kalau bocor, yang keluar
                    hanya angka agregat, bukan daftar per-baris.
                </li>
                <li>
                    <strong class="text-neutral-800 dark:text-neutral-100">Daftar IP</strong> — bekerja
                    di lingkungan ini: aplikasi melihat IP publik asli pemanggil, bukan IP Cloudflare
                    atau proksi. Pakai untuk konsumen server-ke-server yang IP-nya tetap.
                </li>
                <li>
                    <strong class="text-neutral-800 dark:text-neutral-100">Daftar Origin</strong> —
                    untuk aplikasi berbasis browser. Tidak berguna untuk server-ke-server, karena
                    header Origin bisa diisi apa saja oleh pemanggil.
                </li>
            </ul>

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20">
                <p class="text-sm text-amber-800 dark:text-amber-200">
                    Untuk token yang membawa data identitas pegawai atau data keamanan, pasang daftar IP.
                    Kalau IP konsumen tidak tetap, imbangi dengan scope sesempit mungkin dan masa berlaku
                    yang pendek — bukan dengan membiarkan token terbuka selamanya.
                </p>
            </div>
        </x-nawasara-ui::page.card>

    </div>
</x-nawasara-docs::shell>
