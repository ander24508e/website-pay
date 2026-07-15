<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'attended_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $userId = $this->integer('user_id') ?: null;
                $vehicleId = $this->integer('vehicle_id') ?: null;

                if (!$userId || !$vehicleId) {
                    return;
                }

                $vehicleBelongsToClient = \App\Models\Vehicle::query()
                    ->whereKey($vehicleId)
                    ->where('user_id', $userId)
                    ->exists();

                if (!$vehicleBelongsToClient) {
                    $validator->errors()->add('vehicle_id', 'El vehículo seleccionado no pertenece al cliente de la venta.');
                }
            },
        ];
    }
}
