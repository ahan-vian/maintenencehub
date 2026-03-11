<?php

namespace App\Events;

use App\Models\Sensor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorPatched
{
    use Dispatchable, SerializesModels;

    public Sensor $sensor;
    public ?int $userId;
    public array $changes;

    public function __construct(Sensor $sensor, ?int $userId, array $changes)
    {
        $this->sensor = $sensor;
        $this->userId = $userId;
        $this->changes = $changes;
    }
}