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
        // Daftar kapasitas sinkron dengan data material yang ada di ItemSeeder.
        $capacities = [
            ['material_base_name' => 'Gas LPG 3 Kg', 'capacity' => 20000],
            ['material_base_name' => 'Bright Gas 5.5 Kg', 'capacity' => 10000],
            ['material_base_name' => 'LPG 12 Kg', 'capacity' => 0],
            ['material_base_name' => 'Elpiji Industri 50 Kg', 'capacity' => 0],
            ['material_base_name' => 'Solar Industri', 'capacity' => 0],
            ['material_base_name' => 'Oli Mesin', 'capacity' => 0],
            ['material_base_name' => 'Avtur', 'capacity' => 0],
            ['material_base_name' => 'Pertalite', 'capacity' => 0],
            ['material_base_name' => 'Pelumas Fastron', 'capacity' => 0],
            ['material_base_name' => 'Aspal Curah', 'capacity' => 0],
            ['material_base_name' => 'Minyak Tanah', 'capacity' => 0],
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
