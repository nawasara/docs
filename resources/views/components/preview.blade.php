{{--
    Merender cuplikan blade secara hidup, lalu menampilkan sumbernya di bawah.

    Dibungkus try/catch: satu komponen yang gagal dirender tidak boleh
    menjatuhkan seluruh halaman katalog. Yang gagal ditampilkan sebagai
    catatan kecil, dan sisanya tetap terbaca.

    Pemakaian:
        <x-nawasara-docs::preview :code="$markup" />
--}}
@props(['code'])

@php
    $rendered = null;
    $error = null;

    try {
        $rendered = Blade::render($code);
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
@endphp

<div class="mt-3">
    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Tampilan</p>

    @if ($error === null)
        {{-- Latar berbeda dari kartu supaya batas komponen terlihat jelas,
             terutama untuk komponen yang sendirinya berlatar putih. --}}
        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-900/50">
            {!! $rendered !!}
        </div>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 dark:border-amber-800 dark:bg-amber-900/20">
            <p class="text-xs text-amber-800 dark:text-amber-200">
                Tidak bisa dipratinjau di sini: {{ \Illuminate\Support\Str::limit($error, 120) }}
            </p>
        </div>
    @endif

    <div class="mt-2">
        <x-nawasara-docs::code lang="blade">{{ $code }}</x-nawasara-docs::code>
    </div>
</div>
