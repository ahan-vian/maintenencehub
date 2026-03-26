<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Sensor;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolesAndPermissionsSeeder;
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

        $tech = User::where('email', 'tech@test.com')->first();
        $locations = Location::inRandomOrder()->take(3)->get();

        $tech->locations()->sync($locations->pluck('id'));

        User::updateOrCreate(
            ['email' => 'viewer@test.com'],
            ['name' => 'Viewer', 'password' => Hash::make('password123')]
        )->assignRole('viewer');
    }
}