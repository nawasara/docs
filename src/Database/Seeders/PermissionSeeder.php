<?php

namespace Nawasara\Docs\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Izin untuk workspace Dokumentasi.
 *
 * HANYA `view` — tidak ada create/update/delete. Isi dokumentasi dibangun dari
 * berkas dan katalog runtime, bukan dari basis data, jadi tidak ada yang bisa
 * disunting lewat panel; izin tulis hanya akan menggerbang halaman yang tidak
 * ada.
 *
 * ⚠️ Sebelum ini paket docs sama sekali tanpa seeder, dan menu-nya memakai
 * `permission => null` — artinya siapa pun yang punya akun melihat workspace
 * Dokumentasi. Setelah izin ini dipasang, yang belum diberi
 * `docs.page.view` KEHILANGAN menunya. Jalankan seeder ini di setiap
 * lingkungan bersamaan dengan pemasangan versinya, atau staf akan melapor
 * menunya "hilang".
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'docs.page.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Dokumentasi adalah cara memakai sistem, bukan datanya — jadi
        // diberikan ke SEMUA peran yang sudah ada, bukan hanya developer.
        // Menggerbangnya ke developer saja akan mencabut halaman panduan dari
        // justru orang yang paling membutuhkannya: operator OPD yang baru
        // memakai sistemnya.
        //
        // firstOrCreate di atas membuat izinnya; pemberian di bawah memakai
        // givePermissionTo yang idempoten, jadi seeder aman dijalankan ulang.
        Role::all()->each(fn (Role $role) => $role->givePermissionTo($permissions));
    }
}
