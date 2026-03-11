<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Sensor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SensorPatchTest extends TestCase
{
    use RefreshDatabase;

    protected function seedAdmin(): User
    {
        Permission::firstOrCreate(['name' => 'sensors.manage']);
        Permission::firstOrCreate(['name' => 'sensors.read']);
        Permission::firstOrCreate(['name' => 'sensors.delete']);
        Permission::firstOrCreate(['name' => 'locations.read']);
        Permission::firstOrCreate(['name' => 'locations.manage']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $admin = User::factory()->create(['password' => Hash::make('password123')]);
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_patch_status_only(): void
    {
        $admin = $this->seedAdmin();

        $location = Location::factory()->create();
        $sensor = Sensor::factory()->create([
            'location_id' => $location->id,
            'status' => 'active',
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/sensors/{$sensor->id}", [
                'status' => 'maintenance',
            ]);

        $res->assertStatus(200);
        $this->assertDatabaseHas('sensors', [
            'id' => $sensor->id,
            'status' => 'maintenance',
        ]);
    }

    public function test_patch_interval_recalculates_next_due_date(): void
    {
        $admin = $this->seedAdmin();

        $sensor = Sensor::factory()->create([
            'last_calibrated_at' => '2026-03-01',
            'calibration_interval_days' => 90,
            'next_due_date' => '2026-05-30', // 90 hari dari 2026-03-01
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/sensors/{$sensor->id}", [
                'calibration_interval_days' => 120,
            ]);

        $res->assertStatus(200);

        // 2026-03-01 + 120 hari = 2026-06-29
        $this->assertDatabaseHas('sensors', [
            'id' => $sensor->id,
            'calibration_interval_days' => 120,
            'next_due_date' => '2026-06-29',
        ]);
    }

    public function test_patch_last_calibrated_at_null_sets_next_due_date_null(): void
    {
        $admin = $this->seedAdmin();

        $sensor = Sensor::factory()->create([
            'last_calibrated_at' => '2026-03-01',
            'next_due_date' => '2026-05-30',
        ]);

        $res = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/sensors/{$sensor->id}", [
                'last_calibrated_at' => null,
            ]);

        $res->assertStatus(200);

        $this->assertDatabaseHas('sensors', [
            'id' => $sensor->id,
            'last_calibrated_at' => null,
            'next_due_date' => null,
        ]);
    }
}