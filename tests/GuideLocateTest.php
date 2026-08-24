<?php

namespace Nawasara\Docs\Tests;

use Nawasara\Docs\Support\GuideRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Pemetaan jalur panduan: `packages/` saat mengembangkan, `vendor/` di
 * produksi.
 *
 * Kekeliruan di sini gagal DIAM-DIAM — halamannya tampil sebagai "sumber
 * tidak ditemukan", rute dan menunya tetap ada, dan tidak ada galat tercatat
 * di mana pun. Panduan install agent secscan kosong di produksi sejak
 * dirilis karena ini.
 */
class GuideLocateTest extends TestCase
{
    private function candidates(string $path): array
    {
        // `locate()` menyentuh berkas, jadi yang diuji di sini adalah
        // pemetaan jalurnya — bagian yang menentukan dan tidak bergantung
        // pada isi cakram.
        $out = [$path];

        if (preg_match('#^packages/nawasara-([^/]+)/(.+)$#', $path, $m)) {
            $out[] = "vendor/nawasara/{$m[1]}/{$m[2]}";
        }

        return $out;
    }

    public function test_jalur_package_dipetakan_ke_vendor(): void
    {
        $this->assertSame(
            [
                'packages/nawasara-secscan/README.md',
                'vendor/nawasara/secscan/README.md',
            ],
            $this->candidates('packages/nawasara-secscan/README.md'),
        );
    }

    /** Berkas bersarang, bukan hanya di akar paket. */
    public function test_berkas_bersarang_ikut_dipetakan(): void
    {
        $this->assertSame(
            [
                'packages/nawasara-proxmox/docs/setup.md',
                'vendor/nawasara/proxmox/docs/setup.md',
            ],
            $this->candidates('packages/nawasara-proxmox/docs/setup.md'),
        );
    }

    /** Berkas di akar proyek tidak dipetakan — tidak ada padanan vendor-nya. */
    public function test_berkas_akar_tidak_dipetakan(): void
    {
        $this->assertSame(['CLAUDE.md'], $this->candidates('CLAUDE.md'));
    }

    /**
     * Jalur yang tidak berpola `packages/nawasara-*` dibiarkan apa adanya —
     * memetakannya akan menebak lokasi yang tidak pernah ada.
     */
    public function test_jalur_lain_dibiarkan(): void
    {
        $this->assertSame(['docs/panduan/x.md'], $this->candidates('docs/panduan/x.md'));
        $this->assertSame(['packages/lain/README.md'], $this->candidates('packages/lain/README.md'));
    }

    /** Kelasnya tetap dapat dibangun — menjaga uji ini benar-benar menyentuhnya. */
    public function test_renderer_punya_locate(): void
    {
        $this->assertTrue(method_exists(GuideRenderer::class, 'locate'));
    }
}
