<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatchSensorRequest extends FormRequest
{
    public function rules(): array
    {
        $sensorId = $this->route('sensor')?->id;

        return [
            'location_id' => ['sometimes', 'integer', 'exists:locations,id'],
            'name' => ['sometimes', 'string', 'max:150'],
            'serial_number' => [
                'sometimes', 'string', 'max:80',
                Rule::unique('sensors', 'serial_number')->ignore($sensorId),
            ],
            'type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'in:active,inactive,maintenance'],
            'last_calibrated_at' => ['sometimes', 'nullable', 'date'],
            'calibration_interval_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
        ];
    }
}