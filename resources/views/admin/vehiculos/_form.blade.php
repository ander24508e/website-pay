@csrf

@if ($errors->any())
    <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">Revisa los campos marcados.</p>
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-5">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Información principal</p>
            <p class="text-sm text-gray-500 mt-1">Relaciona el vehículo con un cliente y registra su identificación.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Selecciona un cliente</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('user_id', $vehiculo->user_id ?? '') == $client->id)>
                            {{ $client->name }} — {{ $client->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                <input type="text" name="plate" value="{{ old('plate', $vehiculo->plate ?? '') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase" placeholder="Ej: ABC-1234" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <input type="text" name="color" value="{{ old('color', $vehiculo->color ?? '') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Ej: Blanco, negro, rojo">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <input type="number" name="year" value="{{ old('year', $vehiculo->year ?? '') }}"
                    min="1900" max="{{ now()->year + 1 }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="{{ now()->year }}">
            </div>

            <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-gray-900"
                    @checked(old('active', $vehiculo->active ?? true))>
                <div>
                    <p class="text-sm font-medium text-gray-700">Vehículo activo</p>
                    <p class="text-xs text-gray-400">Disponible para usar en ventas y servicios.</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
            <textarea name="observations" rows="4"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                placeholder="Detalles visibles, notas internas o información relevante del vehículo">{{ old('observations', $vehiculo->observations ?? '') }}</textarea>
        </div>
    </section>

    <section class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-5">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Especificaciones</p>
            <p class="text-sm text-gray-500 mt-1">Selecciona datos existentes o crea nuevos desde este formulario.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de vehículo existente</label>
            <select name="vehicle_type_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Selecciona un tipo</option>
                @foreach ($vehicleTypes as $vehicleType)
                    <option value="{{ $vehicleType->id }}" @selected(old('vehicle_type_id', $vehiculo->vehicle_type_id ?? '') == $vehicleType->id)>
                        {{ $vehicleType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo tipo de vehículo</label>
            <input type="text" name="vehicle_type_name" value="{{ old('vehicle_type_name') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                placeholder="Ej: Automóvil, SUV, camioneta grande">
            <p class="text-xs text-gray-400 mt-1">Si escribes un tipo nuevo, tendrá prioridad sobre la selección.</p>
            @error('vehicle_type_id')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
            @error('vehicle_type_name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marca existente</label>
            <select id="vehicleBrandSelect" name="vehicle_brand_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Selecciona una marca</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(old('vehicle_brand_id', $vehiculo->vehicle_brand_id ?? '') == $brand->id)>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva marca</label>
            <input type="text" name="brand_name" value="{{ old('brand_name') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Ej: Toyota, Ford, Chevrolet">
            <p class="text-xs text-gray-400 mt-1">Si escribes una marca nueva, se usará esa sobre la selección.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo existente</label>
            <select id="vehicleModelSelect" name="vehicle_model_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Selecciona un modelo</option>
                @foreach ($models as $model)
                    <option value="{{ $model->id }}" data-brand="{{ $model->vehicle_brand_id }}"
                        @selected(old('vehicle_model_id', $vehiculo->vehicle_model_id ?? '') == $model->id)>
                        {{ $model->brand?->name }} — {{ $model->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo modelo</label>
            <input type="text" name="model_name" value="{{ old('model_name') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Ej: Grand Vitara, F-150">
            <p class="text-xs text-gray-400 mt-1">Si escribes un modelo nuevo, se enlaza con la marca seleccionada o nueva.</p>
        </div>
    </section>
</div>

<div class="flex flex-col sm:flex-row gap-3 justify-end">
    <a href="{{ route('admin.vehiculos.index') }}" class="inline-flex items-center justify-center bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold">
        Cancelar
    </a>
    <button class="inline-flex items-center justify-center bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
        {{ $buttonText ?? 'Guardar vehículo' }}
    </button>
</div>

@push('scripts')
    <script>
        const brandSelect = document.getElementById('vehicleBrandSelect');
        const modelSelect = document.getElementById('vehicleModelSelect');
        const modelOptions = Array.from(modelSelect?.options ?? []);

        function syncVehicleModels() {
            if (!brandSelect || !modelSelect) return;

            const selectedBrand = brandSelect.value;

            modelOptions.forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedBrand && option.dataset.brand !== selectedBrand;
            });

            const selectedOption = modelSelect.options[modelSelect.selectedIndex];
            if (selectedOption?.hidden) {
                modelSelect.value = '';
            }
        }

        brandSelect?.addEventListener('change', syncVehicleModels);
        syncVehicleModels();

    </script>
@endpush
