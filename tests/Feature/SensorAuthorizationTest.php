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

class SensorAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function seedRolesAndPermissions(): void
    {
        // permissions
        $permissions = [
            'locations.read',
            'locations.manage',
            'sensors.read',
            'sensors.manage',
            'sensors.delete',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $tech = Role::firstOrCreate(['name' => 'technician']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        $admin->givePermissionTo($permissions);
        $tech->givePermissionTo(['locations.read', 'sensors.read', 'sensors.manage']);
        $viewer->givePermissionTo(['locations.read', 'sensors.read']);
    }

    protected function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole($role);

        return $user;
    }

    public function test_sensors_requires_auth(): void
    {
        $this->seedRolesAndPermissions();

        $res = $this->getJson('/api/sensors');
        $res->assertStatus(401);
    }

    public function test_viewer_cannot_patch_sensor(): void
    {
        $this->seedRolesAndPermissions();

        $viewer = $this->makeUserWithRole('viewer');

        $location = Location::factory()->create();
        $sensor = Sensor::factory()->create(['location_id' => $location->id]);

        $res = $this->actingAs($viewer, 'sanctum')
            ->patchJson("/api/sensors/{$sensor->id}", ['status' => 'maintenance']);

        $res->assertStatus(403);
    }

    public function test_technician_can_patch_only_assigned_location_sensor(): void
    {
        $this->seedRolesAndPermissions();

        $tech = $this->makeUserWithRole('technician');

        $locAssigned = Location::factory()->create();
        $locOther = Location::factory()->create();

        // assign tech ke 1 location
        $tech->locations()->attach($locAssigned->id);

        $sensorA = Sensor::factory()->create(['location_id' => $locAssigned->id]);
        $sensorB = Sensor::factory()->create(['location_id' => $locOther->id]);

        // sensor di lokasi assigned -> 200
        $ok = $this->actingAs($tech, 'sanctum')
            ->patchJson("/api/sensors/{$sensorA->id}", ['status' => 'maintenance']);
        $ok->assertStatus(200);

        // sensor di lokasi lain -> 403 (policy)
        $forbidden = $this->actingAs($tech, 'sanctum')
            ->patchJson("/api/sensors/{$sensorB->id}", ['status' => 'maintenance']);
        $forbidden->assertStatus(403);
    }

    public function test_admin_can_patch_any_sensor(): void
    {
        $this->seedRolesAndPermissions();

        $admin = $this->makeUserWithRole('admin');

        $sensor = Sensor::factory()->create();

        $res = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/sensors/{$sensor->id}", ['status' => 'inactive']);

        $res->assertStatus(200);
    }
}