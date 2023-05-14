<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class EarthquakeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('earthquake')->insert([
            'magnitude' => ('3.0'),
            'disaster_id' => ('2'),
            'created_at' => Date::now(),
        ]);

        DB::table('earthquake')->insert([
            'magnitude' => ('4.5'),
            'disaster_id' => ('2'),
            'created_at' => Date::now(),
        ]);

        DB::table('earthquake')->insert([
            'magnitude' => ('6.8'),
            'disaster_id' => ('2'),
            'created_at' => Date::now(),
        ]);
    }
}