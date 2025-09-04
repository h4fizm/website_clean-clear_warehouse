<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialCapacity;

class MaterialCapacitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kapasitas sinkron dengan 3 grup material dari ItemSeeder.
        $capacities = [
            ['material_base_name' => 'LPG 3 Kg', 'capacity' => 20000],
            ['material_base_name' => 'LPG 12 Kg', 'capacity' => 0],
            ['material_base_name' => 'Bright Gas 5.5 Kg', 'capacity' => 10000],
        ];

        // Menggunakan updateOrCreate untuk mencegah duplikasi jika seeder dijalankan lagi
        foreach ($capacities as $capacity) {
            MaterialCapacity::updateOrCreate(
                ['material_base_name' => $capacity['material_base_name']],
                ['capacity' => $capacity['capacity']]
            );
        }
    }
}

