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

        // User biasa: cuma boleh export data (sama seperti perilaku sebelumnya).
        $user->syncPermissions(['export-data']);
    }
}
