<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Import Role

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data pengguna yang akan dibuat
        $users = [
            [
                'name' => 'Manager Account',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role' => 'Manager',
            ],
            [
                'name' => 'Admin Plaju',
                'email' => 'admin.plaju@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin P.Layang',
            ],
            [
                'name' => 'Admin Sungai Gerong',
                'email' => 'admin.gerong@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin P.Layang',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Jambi',
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Bengkulu',
            ],
            [
                'name' => 'Doni Saputra',
                'email' => 'doni.lampung@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Lampung',
            ],
            [
                'name' => 'Eka Wijaya',
                'email' => 'eka.jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Jambi',
            ],
            [
                'name' => 'Fitriani',
                'email' => 'fitri.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Bengkulu',
            ],
            [
                'name' => 'Gunawan',
                'email' => 'gunawan.lampung@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Lampung',
            ],
            [
                'name' => 'Herlina',
                'email' => 'herlina.jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Jambi',
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'indra.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'User SA Bengkulu',
            ],
        ];

        // Pastikan role sudah ada sebelum membuat user
        foreach ($users as $data) {
            Role::firstOrCreate(['name' => $data['role']]);
        }

        // Loop untuk membuat user dan memberikan role
        foreach ($users as $data) {
            // Gunakan firstOrCreate untuk menghindari duplikasi email
            $user = User::firstOrCreate(
                ['email' => $data['email']], // Kondisi pengecekan
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ] // Data yang akan dibuat jika email belum ada
            );

            // Berikan role kepada user
            $user->assignRole($data['role']);
        }
    }
}