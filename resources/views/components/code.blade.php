{{--
    Blok kode dengan tombol salin.

    Memakai Alpine magic $clipboard yang sudah terdaftar global di
    resources/js/app.js — tidak perlu setup tambahan di halaman.

    Isi diambil dari slot dan di-escape Blade, jadi markup contoh (yang justru
    sering berisi tag) tampil apa adanya alih-alih dieksekusi browser.

    Pemakaian:
        <x-nawasara-docs::code>{{ '<x-nawasara-ui::badge>Aktif</x-nawasara-ui::badge>' }}</x-nawasara-docs::code>
        <x-nawasara-docs::code lang="bash" :label="'Jalankan di server'">curl -fsSL ...</x-nawasara-docs::code>
--}}
@props([
    'lang' => 'blade',
    'label' => null,
])

@php
    // Slot sudah ter-escape sekali oleh Blade, dan `{{ }}` di bawah akan
    // meng-escape lagi — hasilnya `&lt;x-nawasara-ui::badge` tampil apa adanya
    // di layar alih-alih `<x-nawasara-ui::badge`. Kembalikan ke bentuk mentah
    // di sini supaya escaping tepat terjadi sekali, di tempat yang benar.
    //
    // trim() supaya indentasi blade tidak ikut terbawa ke dalam <pre>, yang
    // akan membuat setiap baris tergeser beberapa spasi tanpa alasan.
    $code = trim(html_entity_decode($slot->toHtml(), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
@endphp

<div class="group relative" x-data="{ copied: false }">
    @if ($label)
        <p class="mb-1 text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ $label }}</p>
    @endif

    <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex items-center justify-between border-b border-neutral-200 px-3 py-1.5 dark:border-neutral-700">
            <span class="font-mono text-[11px] uppercase tracking-wide text-neutral-400 dark:text-neutral-500">{{ $lang }}</span>

            <button type="button"
                x-on:click="$clipboard($refs.source.innerText); copied = true; setTimeout(() => copied = false, 1500)"
                class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-xs text-neutral-500 transition-colors hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200">
                <template x-if="! copied">
                    <span class="inline-flex items-center gap-1"><x-lucide-copy class="size-3.5" /> Salin</span>
                </template>
                <template x-if="copied">
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400"><x-lucide-check class="size-3.5" /> Tersalin</span>
                </template>
            </button>
        </div>

        {{-- overflow-x-auto di sini, bukan di halaman: contoh kode panjang
             harus menggulir di dalam kotaknya sendiri, bukan membuat seluruh
             halaman bergeser ke samping. --}}
        <pre class="overflow-x-auto px-3 py-2.5 text-[13px] leading-relaxed"><code x-ref="source" class="font-mono text-neutral-800 dark:text-neutral-200">{{ $code }}</code></pre>
    </div>
</div>
