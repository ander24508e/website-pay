@php
    $inputClass =
        'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
    $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
    $selectedVehicleTypeId = old('vehicle_type_id', $serviceVehicleTypePrice?->vehicle_type_id);
    $supplyRows = old('supplies');

    if ($supplyRows === null) {
        $supplyRows = collect($supplies ?? [])
            ->map(
                fn($supply) => [
                    'catalog_item_variant_id' => $supply->catalog_item_variant_id,
                    'quantity' => $supply->quantity,
                    'unit' => $supply->unit,
                ],
            )
            ->values()
            ->all();
    }

    while (count($supplyRows) < 4) {
        $supplyRows[] = ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''];
    }
@endphp

<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">
    <div class="space-y-4">
        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Servicio padre</p>
            <label class="{{ $labelClass }}">Servicio</label>
            <input type="text" value="{{ $service->type?->name ?? 'Servicio' }} / {{ $service->name }}"
                class="{{ $inputClass }} bg-white text-gray-600" readonly>
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Precio por vehiculo</p>
            <p class="mb-3 text-xs text-gray-500">Define cuanto cuesta este servicio para un grupo de vehiculos.</p>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Tipo de vehiculo *</label>
                    <select name="vehicle_type_id" data-placeholder="Selecciona tipo"
                        class="select2 {{ $inputClass }} bg-white @error('vehicle_type_id') border-red-400 bg-red-50 @enderror">
                        <option value="">Selecciona tipo</option>
                        @foreach ($vehicleTypes as $vehicleType)
                            <option value="{{ $vehicleType->id }}" @selected((string) $selectedVehicleTypeId === (string) $vehicleType->id)>
                                {{ $vehicleType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_type_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <select class="js-example-basic-multiple" multiple="multiple" name="states[]">
                    <option value="AL">
                        Alabama
                    </option>
                    ...
                    <option value="WY">
                        Wyoming
                    </option>
                </select>

                <div>
                    <label class="{{ $labelClass }}">Crear tipo nuevo</label>
                    <input type="text" name="new_vehicle_type_name" value="{{ old('new_vehicle_type_name') }}"
                        class="{{ $inputClass }} bg-white @error('new_vehicle_type_name') border-red-400 bg-red-50 @enderror"
                        placeholder="Ej: Auto pequeno, SUV">
                    @error('new_vehicle_type_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Precio *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm font-semibold text-gray-400">$</span>
                        <input type="number" name="price"
                            value="{{ old('price', $serviceVehicleTypePrice?->price) }}" step="0.01" min="0"
                            class="{{ $inputClass }} bg-white pl-8 @error('price') border-red-400 bg-red-50 @enderror"
                            placeholder="0.00">
                    </div>
                    @error('price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Duracion</label>
                    <input type="number" name="duration_minutes"
                        value="{{ old('duration_minutes', $serviceVehicleTypePrice?->duration_minutes) }}"
                        min="1" step="1"
                        class="{{ $inputClass }} bg-white @error('duration_minutes') border-red-400 bg-red-50 @enderror"
                        placeholder="Minutos">
                    @error('duration_minutes')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <label class="{{ $labelClass }}">Descripcion</label>
                <textarea name="description" rows="3"
                    class="{{ $inputClass }} resize-none bg-white @error('description') border-red-400 bg-red-50 @enderror"
                    placeholder="Notas internas para este precio">{{ old('description', $serviceVehicleTypePrice?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Insumos usados</p>
            <p class="mb-3 text-xs text-gray-500">Opcional. Registra productos que se consumen al realizar este
                servicio.</p>

            @if ($supplyVariants->isEmpty())
                <p class="rounded-lg border border-gray-100 bg-white p-3 text-xs text-gray-500">
                    No hay productos inventariables para usar como insumos.
                </p>
            @else
                <div class="space-y-2">
                    @foreach ($supplyRows as $index => $supply)
                        <div
                            class="grid gap-2 rounded-lg border border-gray-100 bg-white p-3 sm:grid-cols-[minmax(0,1fr)_120px_120px]">
                            <select name="supplies[{{ $index }}][catalog_item_variant_id]"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                <option value="">Selecciona insumo</option>
                                @foreach ($supplyVariants as $variant)
                                    <option value="{{ $variant->id }}" @selected((string) ($supply['catalog_item_variant_id'] ?? '') === (string) $variant->id)>
                                        {{ $variant->item?->name }} / {{ $variant->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" name="supplies[{{ $index }}][quantity]"
                                value="{{ $supply['quantity'] ?? '' }}" step="0.001" min="0.001"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm"
                                placeholder="Cantidad">
                            <input type="text" name="supplies[{{ $index }}][unit]"
                                value="{{ $supply['unit'] ?? '' }}"
                                class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm"
                                placeholder="Unidad">
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <aside class="space-y-4">
        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Estado</p>
            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                <input type="checkbox" name="active" value="1"
                    {{ old('active', $serviceVehicleTypePrice?->active ?? true) ? 'checked' : '' }}
                    class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                <span>
                    <span class="block text-sm font-semibold text-gray-700">Activo</span>
                    <span class="block text-xs text-gray-400">Disponible para ventas y web</span>
                </span>
            </label>
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Resumen</p>
            <p class="mt-2 text-sm text-gray-600">
                El cliente buscara su vehiculo real. El sistema usara este tipo interno para calcular el precio.
            </p>
        </section>
    </aside>
</div>
