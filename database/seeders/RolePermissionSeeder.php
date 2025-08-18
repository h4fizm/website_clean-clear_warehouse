<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        $permissions = [
            'manage data playang',
            'manage transaksi',
            'manage item',
            'manage aktivitas harian',
            'manage user',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat roles
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
            $role = Role::firstOrCreate(['name' => $roleName]);

            switch ($roleName) {
                case 'Manager':
                    $role->givePermissionTo([
                        'manage data playang',
                        'manage transaksi',
                        'manage item',
                        'manage aktivitas harian',
                        'manage user',
                    ]);
                    break;

                case 'Admin P.Layang':
                    $role->givePermissionTo([
                        'manage data playang',
                        'manage transaksi',
                        'manage item',
                        'manage aktivitas harian',
                    ]);
                    break;

                default: // untuk semua SA
                    $role->givePermissionTo([
                        'manage data playang',
                        'manage transaksi',
                        'manage item',
                        'manage aktivitas harian',
                    ]);
                    break;
            }
        }
    }
}
