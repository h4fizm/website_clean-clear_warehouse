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
        $materials = [
            'Gas LPG 3 Kg',
            'Bright Gas 5.5 Kg',
            'LPG 12 Kg',
            'Elpiji Industri 50 Kg',
            'Solar Industri',
            'Oli Mesin',
            'Avtur',
            'Pertalite',
            'Pelumas Fastron',
            'Aspal Curah',
            'Minyak Tanah',
        ];

        // Create capacities for multiple months and years
        foreach ($materials as $material) {
            for ($year = 2024; $year <= 2025; $year++) {
                for ($month = 1; $month <= 12; $month++) {
                    $capacityValue = 0;
                    if ($material === 'Gas LPG 3 Kg' && $year == 2024 && $month == 1) {
                        $capacityValue = 20000;
                    } elseif ($material === 'Bright Gas 5.5 Kg' && $year == 2024 && $month == 1) {
                        $capacityValue = 10000;
                    }
                    
                    MaterialCapacity::updateOrCreate(
                        [
                            'material_base_name' => $material,
                            'month' => $month,
                            'year' => $year,
                        ],
                        ['capacity' => $capacityValue]
                    );
                }
            }
        }
    }
}
