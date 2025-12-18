<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

final class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // master
            'manage-master',         // prodi, mata_kuliah, ruangan, dsb
            'manage-users',          // mahasiswa/dosen/staff
            'manage-jadwal',
            'manage-kelas',

            // akademik
            'ambil-krs',
            'approve-krs',
            'view-krs',
            'input-nilai',
            'finalize-nilai',
            'view-nilai',
            'view-jadwal',
        ];

        foreach ($permissions as $p) {
            Permission::findOrCreate($p, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $dosen = Role::findOrCreate('dosen', 'web');
        $mhs   = Role::findOrCreate('mahasiswa', 'web');

        $admin->syncPermissions($permissions);

        $dosen->syncPermissions([
            'view-jadwal',
            'view-krs',
            'input-nilai',
            'finalize-nilai',
            'view-nilai',
        ]);

        $mhs->syncPermissions([
            'view-jadwal',
            'ambil-krs',
            'view-krs',
            'view-nilai',
        ]);
    }
}
