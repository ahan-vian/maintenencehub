<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

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
        $technician = Role::firstOrCreate(['name' => 'technician']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // assign permissions
        $admin->givePermissionTo($permissions);

        $technician->givePermissionTo([
            'locations.read',
            'sensors.read',
            'sensors.manage',
        ]);

        $viewer->givePermissionTo([
            'locations.read',
            'sensors.read',
        ]);
    }
}