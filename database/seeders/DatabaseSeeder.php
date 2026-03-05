<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Sensor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Location::factory()
            ->count(10)
            ->create();

        Sensor::factory()
            ->count(50)
            ->create();
    }
}