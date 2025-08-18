<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role' => 'Manager',
            ],
            [
                'name' => 'admin_playang',
                'email' => 'playang@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin P.Layang',
            ],
            [
                'name' => 'sa_jambi',
                'email' => 'jambi@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Jambi',
            ],
            [
                'name' => 'sa_bengkulu',
                'email' => 'bengkulu@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Bengkulu',
            ],
            [
                'name' => 'sa_lampung',
                'email' => 'lampung@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Lampung',
            ],
            [
                'name' => 'sa_sumsel',
                'email' => 'sumsel@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Sumsel',
            ],
            [
                'name' => 'sa_babel',
                'email' => 'babel@example.com',
                'password' => Hash::make('password'),
                'role' => 'SA Babel',
            ],
        ];

        foreach ($users as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->assignRole($data['role']);
        }
    }
}
