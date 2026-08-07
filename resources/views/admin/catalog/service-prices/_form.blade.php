@php
    $inputClass =
        'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
    $quickInputClass =
        'h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-700 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200';
    $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
    $selectedSpecification = $serviceVehicleTypePrice?->vehicleSpecification;
    $selectedSpecificationId = old('vehicle_specification_id', $selectedSpecification?->id);
    $vehicleSpecifications = $vehicleSpecifications ?? collect();
    $creatingNewSpecification =
        old('new_vehicle_brand_name') ||
        old('new_vehicle_model_name') ||
        old('new_vehicle_type_name') ||
        $errors->has('new_vehicle_brand_name') ||
        $errors->has('new_vehicle_model_name') ||
        $errors->has('new_vehicle_type_name');
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

<div class="catalog-service-price-form grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">
    <div class="space-y-4">
        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <label class="{{ $labelClass }}">Servicio</label>
            <input type="text" value="{{ $service->type?->name ?? 'Servicio' }} / {{ $service->name }}"
                class="{{ $inputClass }} bg-white text-gray-600" readonly>

            <div class="mt-5 {{ $creatingNewSpecification ? 'hidden' : '' }}" data-existing-specification-panel>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <label class="{{ $labelClass }}">Especificaci&oacute;n de veh&iacute;culo existente</label>
                    <button type="button"
                        class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-gray-700"
                        data-show-new-specification>
                        + Crear nueva especificaci&oacute;n
                    </button>
                </div>
                <select name="vehicle_specification_id" data-placeholder="Busca marca / modelo / tipo"
                    class="select2 w-full @error('vehicle_specification_id') border-red-400 @enderror">
                    <option value="">Selecciona una especificaci&oacute;n</option>
                    @foreach ($vehicleSpecifications as $vehicleSpecification)
                        <option value="{{ $vehicleSpecification->id }}" @selected((string) $selectedSpecificationId === (string) $vehicleSpecification->id)>
                            {{ $vehicleSpecification->label }}
                        </option>
                    @endforeach
                </select>
                @error('vehicle_specification_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    Si ya existe el veh&iacute;culo, selecci&oacute;nalo aqu&iacute; y solo configura precio, duraci&oacute;n e insumos para este servicio.
                </p>
            </div>

            <div class="mt-5 {{ $creatingNewSpecification ? '' : 'hidden' }}" data-new-specification-panel>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Crear nueva especificaci&oacute;n</p>
                            <p class="mt-1 text-xs text-gray-500">Usa estos campos &uacute;nicamente cuando no encuentres la combinaci&oacute;n de marca, modelo y tipo.</p>
                        </div>
                        <button type="button"
                            class="rounded-lg bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-200"
                            data-show-existing-specification>
                            Usar especificaci&oacute;n existente
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="min-w-0">
                        <label class="{{ $labelClass }}">Crear marca nueva</label>
                        <input type="text" name="new_vehicle_brand_name" value="{{ old('new_vehicle_brand_name') }}"
                            class="{{ $quickInputClass }} @error('new_vehicle_brand_name') border-red-400 bg-red-50 @enderror"
                            placeholder="Ej: Toyota, Kia">
                        @error('new_vehicle_brand_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label class="{{ $labelClass }}">Crear modelo nuevo</label>
                        <input type="text" name="new_vehicle_model_name" value="{{ old('new_vehicle_model_name') }}"
                            class="{{ $quickInputClass }} @error('new_vehicle_model_name') border-red-400 bg-red-50 @enderror"
                            placeholder="Ej: Corolla, Picanto">
                        @error('new_vehicle_model_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="min-w-0">
                        <label class="{{ $labelClass }}">Crear tipo nuevo</label>
                        <input type="text" name="new_vehicle_type_name" value="{{ old('new_vehicle_type_name') }}"
                            class="{{ $quickInputClass }} @error('new_vehicle_type_name') border-red-400 bg-red-50 @enderror"
                            placeholder="Ej: Auto peque&ntilde;o, SUV">
                        @error('new_vehicle_type_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
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

            <div class="mt-5 border-t border-gray-200 pt-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Insumos usados</p>
                <p class="mb-3 text-xs text-gray-500">Opcional. Registra productos que se consumen al realizar este servicio.</p>

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
            </div>
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
                El cliente buscara su vehiculo real. El sistema conectara marca, modelo y tipo para calcular el precio.
            </p>
        </section>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.catalog-service-price-form').forEach((form) => {
                    const existingPanel = form.querySelector('[data-existing-specification-panel]');
                    const newPanel = form.querySelector('[data-new-specification-panel]');
                    const showNewButton = form.querySelector('[data-show-new-specification]');
                    const showExistingButton = form.querySelector('[data-show-existing-specification]');
                    const specificationSelect = form.querySelector('select[name="vehicle_specification_id"]');
                    const newInputs = form.querySelectorAll(
                        'input[name="new_vehicle_brand_name"], input[name="new_vehicle_model_name"], input[name="new_vehicle_type_name"]'
                    );

                    if (!existingPanel || !newPanel || !showNewButton || !showExistingButton) {
                        return;
                    }

                    const clearSelect2 = (select) => {
                        if (!select) {
                            return;
                        }

                        select.value = '';

                        if (window.jQuery && window.jQuery.fn.select2) {
                            window.jQuery(select).val('').trigger('change');
                        }
                    };

                    showNewButton.addEventListener('click', () => {
                        existingPanel.classList.add('hidden');
                        newPanel.classList.remove('hidden');
                        clearSelect2(specificationSelect);
                    });

                    showExistingButton.addEventListener('click', () => {
                        newPanel.classList.add('hidden');
                        existingPanel.classList.remove('hidden');
                        newInputs.forEach((input) => {
                            input.value = '';
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
