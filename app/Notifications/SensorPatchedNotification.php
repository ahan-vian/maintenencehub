<?php

namespace App\Notifications;

use App\Models\Sensor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SensorPatchedNotification extends Notification
{
    use Queueable;

    public Sensor $sensor;
    public array $changes;
    public ?int $actorId;

    public function __construct(Sensor $sensor, array $changes, ?int $actorId = null)
    {
        $this->sensor = $sensor;
        $this->changes = $changes;
        $this->actorId = $actorId;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Sensor patched',
            'message' => 'A sensor has been updated successfully.',
            'sensor_id' => $this->sensor->id,
            'sensor_name' => $this->sensor->name,
            'location_id' => $this->sensor->location_id,
            'changes' => $this->changes,
            'actor_id' => $this->actorId,
            'patched_at' => now()->toDateTimeString(),
        ];
    }
}