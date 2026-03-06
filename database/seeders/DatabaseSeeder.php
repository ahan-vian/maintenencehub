<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Sensor;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        Location::factory()
            ->count(10)
            ->create();

        Sensor::factory()
            ->count(50)
            ->create();

        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin', 'password' => Hash::make('password123')]
        )->assignRole('admin');

        User::updateOrCreate(
            ['email' => 'tech@test.com'],
            ['name' => 'Technician', 'password' => Hash::make('password123')]
        )->assignRole('technician');

        User::updateOrCreate(
            ['email' => 'viewer@test.com'],
            ['name' => 'Viewer', 'password' => Hash::make('password123')]
        )->assignRole('viewer');
    }
}