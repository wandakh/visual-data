<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Role 'admin' dan 'user', plus permission granular per-aksi. Role cuma
 * "label", permission yang beneran dicek di route/kode. Ini bikin gampang
 * kalau nanti mau nambah role baru (misal 'supervisor') dengan kombinasi
 * akses yang beda, tanpa perlu ubah logic di controller.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'create-data',
            'edit-data',
            'delete-data',
            'import-excel',
            'export-data',
            'view-margin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Admin dapat semua permission.
        $admin->syncPermissions($permissions);

        // Karyawan: boleh tambah, edit, import, export data (dibatasi ke
        // ORG_CODE mereka sendiri di level query, bukan di level permission).
        // TIDAK dapat delete-data (gak boleh hapus/kelola Trash) atau
        // view-margin (data finansial cuma buat Admin).
        $user->syncPermissions(['create-data', 'edit-data', 'import-excel', 'export-data']);
    }
}
