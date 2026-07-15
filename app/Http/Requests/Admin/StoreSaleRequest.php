<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $method = $this->input('payment.method', 'cash');

        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'attended_by' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_id' => ['required', 'integer', 'exists:catalog_items,id'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'items.*.vehicle_type_id' => ['nullable', 'integer', 'exists:vehicle_types,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],

            'payment.method' => ['required', Rule::in(['cash', 'payphone', 'transfer', 'card', 'credit'])],
            'payment.received_amount' => ['nullable', 'numeric', 'min:0', Rule::requiredIf($method === 'cash')],
            'payment.transaction_id' => ['nullable', 'string', 'max:255', Rule::requiredIf($method === 'payphone')],
            'payment.bank' => ['nullable', 'string', 'max:255', Rule::requiredIf($method === 'transfer')],
            'payment.reference' => ['nullable', 'string', 'max:255', Rule::requiredIf($method === 'transfer')],
            'payment.proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:4096'],
            'payment.authorization_code' => ['nullable', 'string', 'max:255', Rule::requiredIf($method === 'card')],
            'payment.due_date' => ['nullable', 'date', Rule::requiredIf($method === 'credit')],
            'payment.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Agrega al menos un item a la venta.',
            'payment.method.required' => 'Selecciona un metodo de pago.',
            'payment.received_amount.required_if' => 'Ingresa el monto recibido en efectivo.',
            'payment.transaction_id.required_if' => 'Ingresa el ID de transaccion PayPhone.',
            'payment.bank.required_if' => 'Ingresa el banco de la transferencia.',
            'payment.reference.required_if' => 'Ingresa la referencia de la transferencia.',
            'payment.authorization_code.required_if' => 'Ingresa el codigo de autorizacion.',
            'payment.due_date.required_if' => 'Ingresa la fecha de vencimiento del credito.',
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

                $vehicleIds = collect($this->input('items', []))
                    ->pluck('vehicle_id')
                    ->filter()
                    ->push($this->input('vehicle_id'))
                    ->filter()
                    ->unique()
                    ->values();

                if ($vehicleIds->isEmpty()) {
                    return;
                }

                $invalidVehicleExists = \App\Models\Vehicle::query()
                    ->whereIn('id', $vehicleIds)
                    ->where('user_id', '!=', $userId)
                    ->exists();

                if ($invalidVehicleExists) {
                    $validator->errors()->add('vehicle_id', 'El vehículo seleccionado no pertenece al cliente de la venta.');
                }
            },
        ];
    }
}
