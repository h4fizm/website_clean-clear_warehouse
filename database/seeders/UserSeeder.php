<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
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
                'role' => 'SA Jambi',
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Bengkulu',
            ],
            [
                'name' => 'Doni Saputra',
                'email' => 'doni.lampung@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Lampung',
            ],
            [
                'name' => 'Eka Wijaya',
                'email' => 'eka.jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Jambi',
            ],
            [
                'name' => 'Fitriani',
                'email' => 'fitri.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Bengkulu',
            ],
            [
                'name' => 'Gunawan',
                'email' => 'gunawan.lampung@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Lampung',
            ],
            [
                'name' => 'Herlina',
                'email' => 'herlina.jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Jambi',
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'indra.bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Bengkulu',
            ],
        ];

        // Loop untuk membuat user dan memberikan role
        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );

            $user->assignRole($data['role']);
        }
    }
}
