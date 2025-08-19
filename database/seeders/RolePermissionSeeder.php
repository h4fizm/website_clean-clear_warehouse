<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🔥 Hapus semua role dan permission lama sebelum buat baru
        Role::query()->delete();
        Permission::query()->delete();

        // Buat permissions
        $permissions = [
            'manage data playang',
            'manage transaksi',
            'manage item',
            'manage aktivitas harian',
            'manage user',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat roles (hanya 7 saja)
        $roles = [
            'Manager',
            'Admin P.Layang',
            'SA Jambi',
            'SA Bengkulu',
            'SA Lampung',
            'SA Sumsel',
            'SA Babel',
        ];

        foreach ($roles as $roleName) {
            $role = Role::create(['name' => $roleName]);

            if ($roleName === 'Manager') {
                $role->syncPermissions($permissions);
            } elseif ($roleName === 'Admin P.Layang') {
                $role->syncPermissions([
                    'manage data playang',
                    'manage transaksi',
                    'manage item',
                    'manage aktivitas harian',
                ]);
            } else {
                $role->syncPermissions([
                    'manage data playang',
                    'manage transaksi',
                    'manage item',
                    'manage aktivitas harian',
                ]);
            }
        }
    }
}
