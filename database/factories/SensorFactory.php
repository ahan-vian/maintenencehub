<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class SensorFactory extends Factory
{
    public function definition(): array
    {
        $last = $this->faker->optional()->dateTimeBetween('-180 days', 'now');
        $interval = $this->faker->randomElement([30, 60, 90, 180]);

        return [
            'location_id' => Location::factory(),
            'name' => $this->faker->randomElement(['CO2 Sensor', 'PM2.5 Sensor', 'TempHum Sensor']) . ' ' . $this->faker->numberBetween(1, 200),
            'serial_number' => strtoupper($this->faker->unique()->bothify('SN-####-????')),
            'type' => $this->faker->randomElement(['CO2', 'PM2.5', 'TempHum']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'maintenance']),
            'last_calibrated_at' => $last?->format('Y-m-d'),
            'calibration_interval_days' => $interval,
            'next_due_date' => $last ? (new \DateTime($last->format('Y-m-d')))->modify("+{$interval} days")->format('Y-m-d') : null,
        ];
    }
}