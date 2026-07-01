@csrf

<div class="space-y-4">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Marca *</label>
        <input type="text" name="brand_name" value="{{ old('brand_name') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
            placeholder="Ej: Toyota, Ford, Chevrolet" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Modelo *</label>
        <input type="text" name="model_name" value="{{ old('model_name') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
            placeholder="Ej: Grand Vitara, F-150" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Tipo de vehículo *</label>
        <input type="text" name="vehicle_type_name" value="{{ old('vehicle_type_name') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
            placeholder="Ej: Sedán, SUV, camioneta grande" required>
    </div>

    <input type="hidden" name="active" value="1">
</div>
