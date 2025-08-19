<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('regions')->insert([
            ['name_region' => 'P.Layang (Pusat)'],
            ['name_region' => 'SA Jambi'],
            ['name_region' => 'SA Bengkulu'],
            ['name_region' => 'SA Lampung'],
            ['name_region' => 'SA Sumsel'],
            ['name_region' => 'SA Babel'],
        ]);
    }
}