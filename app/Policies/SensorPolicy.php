<?php

namespace App\Policies;

use App\Models\Sensor;
use App\Models\User;

class SensorPolicy
{
    public function viewAny(User $user): bool
    {
        // masih bisa ditopang permission middleware, tapi policy juga boleh
        return $user->can('sensors.read');
    }

    public function view(User $user, Sensor $sensor): bool
    {
        return $user->can('sensors.read');
    }

    public function create(User $user): bool
    {
        return $user->can('sensors.manage');
    }

    public function update(User $user, Sensor $sensor): bool
    {
        // Admin: bebas
        if ($user->hasRole('admin'))
            return true;

        // Viewer: tidak boleh update
        if ($user->hasRole('viewer'))
            return false;

        // Technician: boleh update jika dia assigned ke location sensor tsb
        // plus punya permission manage
        if ($user->hasRole('technician') && $user->can('sensors.manage')) {
            return $user->locations()->where('locations.id', $sensor->location_id)->exists();
        }

        return false;
    }

    public function delete(User $user, Sensor $sensor): bool
    {
        // Hanya admin
        return $user->hasRole('admin') && $user->can('sensors.delete');
    }
}