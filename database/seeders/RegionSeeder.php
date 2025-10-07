<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['name_region' => 'P.Layang (Pusat)'],
            ['name_region' => 'Jawa Barat'],
            ['name_region' => 'Jawa Tengah'],
            ['name_region' => 'Jawa Timur'],
            ['name_region' => 'DKI Jakarta'],
            ['name_region' => 'Banten'],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}