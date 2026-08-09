<?php

namespace App\Http\Requests\Admin;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'attended_by' => ['nullable', 'integer', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_id' => ['required', 'integer', 'exists:catalog_items,id'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'items.*.vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'items.*.vehicle_specification_id' => ['nullable', 'integer', 'exists:vehicle_specifications,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Agrega al menos un ítem a la venta.',
            'scheduled_at.required' => 'Ingresa la fecha y hora de entrega esperada.',
            'scheduled_at.after_or_equal' => 'La entrega esperada no puede quedar en el pasado.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $userId = $this->integer('user_id') ?: null;

                if (!$userId) {
                    return;
                }

                $vehicleIds = collect($this->input('items', []))->pluck('vehicle_id')->filter()->unique()->values();

                if ($vehicleIds->isEmpty()) {
                    return;
                }

                $invalidVehicleExists = Vehicle::query()
                    ->whereIn('id', $vehicleIds)
                    ->where('user_id', '!=', $userId)
                    ->exists();

                if ($invalidVehicleExists) {
                    $validator->errors()->add('items', 'Uno de los vehículos seleccionados no pertenece al cliente de la venta.');
                }
            },
        ];
    }
}
