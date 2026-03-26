<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateSensorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $sensorId = $this->route('sensor')?->id;

        return [
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'name' => ['required', 'string', 'max:150'],
            'serial_number' => [
                'required',
                'string',
                'max:80',
                Rule::unique('sensors', 'serial_number')->ignore($sensorId),
            ],
            'type' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:active,inactive,maintenance'],
            'last_calibrated_at' => ['nullable', 'date'],
            'calibration_interval_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ];
    }
}
