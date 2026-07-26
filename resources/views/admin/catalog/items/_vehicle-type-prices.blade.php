@php
    $savedVehicleTypePrices = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('price', 'vehicle_type_id')
        : collect();
    $savedVehicleTypeDurations = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('duration_minutes', 'vehicle_type_id')
        : collect();
    $basePrice = old('base_price', isset($catalogItem) ? $catalogItem->base_price : null);
    $baseDuration = old('duration_minutes', isset($catalogItem) ? $catalogItem->duration_minutes : null);
    $basePricePlaceholder = is_numeric($basePrice) ? '$' . number_format((float) $basePrice, 2) : 'Precio base';
    $baseDurationPlaceholder = is_numeric($baseDuration) ? $baseDuration . ' min' : 'Duración base';
@endphp

<section class="mt-8 border-t border-gray-100 pt-6 space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Precios y duración por tipo de vehículo</p>
            <p class="text-sm text-gray-500 mt-1">
                Configura solo los tipos atendidos. Si precio o duración quedan vacíos, se usará el valor base del servicio.
            </p>
        </div>
        @if (!empty($quickVehicleModalAvailable))
            <button type="button" data-open-vehicle-modal="{{ $vehicleModalId ?? 'serviceVehicleTypesModal' }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-700">
                + Agregar tipo de vehículo
            </button>
        @endif
    </div>

    @if ($vehicleTypes->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            @if (empty($quickVehicleModalAvailable))
                <span>Aún no existen tipos de vehículo.</span>
                <a href="{{ route('admin.vehiculos.create') }}" class="ml-1 font-semibold underline">
                    Crear vehículo
                </a>
            @else
                Aún no existen tipos de vehículo. Usa el botón + Agregar tipo de vehículo para crear uno.
            @endif
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200">
            <div class="grid grid-cols-1 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 text-xs font-semibold uppercase text-gray-500 sm:grid-cols-[minmax(0,1fr)_140px_140px]">
                <span>Tipo de vehículo</span>
                <span class="text-center">Precio</span>
                <span class="text-center">Duración</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($vehicleTypes as $vehicleType)
                    @php
                        $priceFieldName = 'vehicle_type_prices.' . $vehicleType->id;
                        $durationFieldName = 'vehicle_type_durations.' . $vehicleType->id;
                        $currentPrice = old($priceFieldName, $savedVehicleTypePrices->get($vehicleType->id));
                        $currentDuration = old($durationFieldName, $savedVehicleTypeDurations->get($vehicleType->id));
                    @endphp
                    <div class="grid grid-cols-1 gap-3 px-4 py-3 sm:grid-cols-[minmax(0,1fr)_140px_140px] sm:items-center">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800">{{ $vehicleType->name }}</p>
                            @if ($vehicleType->description)
                                <p class="mt-0.5 text-xs text-gray-400">{{ $vehicleType->description }}</p>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" name="vehicle_type_prices[{{ $vehicleType->id }}]"
                                value="{{ $currentPrice }}" step="0.01" min="0"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pl-7 pr-3 text-center text-sm"
                                placeholder="{{ $basePricePlaceholder }}">
                        </div>
                        <input type="number" name="vehicle_type_durations[{{ $vehicleType->id }}]"
                            value="{{ $currentDuration }}" step="1" min="1"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-sm"
                            placeholder="{{ $baseDurationPlaceholder }}">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
