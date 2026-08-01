@php
    $savedVehicleSpecificationPrices = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('price', 'vehicle_specification_id')
        : collect();
    $savedVehicleSpecificationDurations = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('duration_minutes', 'vehicle_specification_id')
        : collect();
    $basePrice = old('base_price', isset($catalogItem) ? $catalogItem->base_price : null);
    $baseDuration = old('duration_minutes', isset($catalogItem) ? $catalogItem->duration_minutes : null);
    $basePricePlaceholder = is_numeric($basePrice) ? '$' . number_format((float) $basePrice, 2) : 'Precio';
    $baseDurationPlaceholder = is_numeric($baseDuration) ? $baseDuration . ' min' : 'Duración';
@endphp

<section class="mt-8 border-t border-gray-100 pt-6 space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Precios por especificación de vehículo</p>
            <p class="mt-1 text-xs text-gray-500">Usa marca, modelo y tipo registrados en Vehículos. Si un precio queda vacío, se usa el precio del servicio.</p>
        </div>
        @if (!empty($quickVehicleModalAvailable))
            <a href="{{ route('admin.vehiculos.specifications.index') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-700">
                + Gestionar especificaciones
            </a>
        @endif
    </div>

    @if ($vehicleSpecifications->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            @if (empty($quickVehicleModalAvailable))
                <span>Aún no existen especificaciones de vehículo.</span>
                <a href="{{ route('admin.vehiculos.create') }}" class="ml-1 font-semibold underline">
                    Crear vehículo
                </a>
            @else
                Aún no existen especificaciones de vehículo. Usa el botón + Gestionar especificaciones para crear una.
            @endif
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200">
            <div class="grid grid-cols-1 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 text-xs font-semibold uppercase text-gray-500 lg:grid-cols-[1fr_1fr_1fr_130px_130px]">
                <span>Marca</span>
                <span>Modelo</span>
                <span>Tipo</span>
                <span class="text-center">Precio</span>
                <span class="text-center">Duración</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($vehicleSpecifications as $vehicleSpecification)
                    @php
                        $priceFieldName = 'vehicle_specification_prices.' . $vehicleSpecification->id;
                        $durationFieldName = 'vehicle_specification_durations.' . $vehicleSpecification->id;
                        $currentPrice = old($priceFieldName, $savedVehicleSpecificationPrices->get($vehicleSpecification->id));
                        $currentDuration = old($durationFieldName, $savedVehicleSpecificationDurations->get($vehicleSpecification->id));
                    @endphp
                    <div class="grid grid-cols-1 gap-3 px-4 py-3 lg:grid-cols-[1fr_1fr_1fr_130px_130px] lg:items-center">
                        <p class="min-w-0 truncate font-semibold text-gray-800">{{ $vehicleSpecification->brand?->name ?? '-' }}</p>
                        <p class="min-w-0 truncate text-gray-700">{{ $vehicleSpecification->model?->name ?? '-' }}</p>
                        <p class="min-w-0 truncate text-gray-700">{{ $vehicleSpecification->type?->name ?? '-' }}</p>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" name="vehicle_specification_prices[{{ $vehicleSpecification->id }}]"
                                value="{{ $currentPrice }}" step="0.01" min="0"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pl-7 pr-3 text-center text-sm"
                                placeholder="{{ $basePricePlaceholder }}">
                        </div>
                        <input type="number" name="vehicle_specification_durations[{{ $vehicleSpecification->id }}]"
                            value="{{ $currentDuration }}" step="1" min="1"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-sm"
                            placeholder="{{ $baseDurationPlaceholder }}">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
