@php
    $savedPrices = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('price', 'vehicle_type_id')
        : collect();
    $savedDurations = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('duration_minutes', 'vehicle_type_id')
        : collect();
@endphp

<section class="mt-8 space-y-4 border-t border-gray-100 pt-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Precios por vehiculo</p>
        <p class="mt-1 text-xs text-gray-500">Configura precios por tipo interno. El cliente seguira buscando por su vehiculo real.</p>
    </div>

    @if (($vehicleTypes ?? collect())->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Aun no existen tipos de vehiculo.
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200">
            <div class="grid grid-cols-1 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-3 text-xs font-semibold uppercase text-gray-500 lg:grid-cols-[1fr_140px_140px]">
                <span>Tipo</span>
                <span class="text-center">Precio</span>
                <span class="text-center">Duracion</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($vehicleTypes as $vehicleType)
                    @php
                        $priceFieldName = 'vehicle_type_prices.' . $vehicleType->id;
                        $durationFieldName = 'vehicle_type_durations.' . $vehicleType->id;
                        $currentPrice = old($priceFieldName, $savedPrices->get($vehicleType->id));
                        $currentDuration = old($durationFieldName, $savedDurations->get($vehicleType->id));
                    @endphp
                    <div class="grid grid-cols-1 gap-3 px-4 py-3 lg:grid-cols-[1fr_140px_140px] lg:items-center">
                        <p class="min-w-0 truncate font-semibold text-gray-800">{{ $vehicleType->name }}</p>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" name="vehicle_type_prices[{{ $vehicleType->id }}]"
                                value="{{ $currentPrice }}" step="0.01" min="0"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pl-7 pr-3 text-center text-sm"
                                placeholder="0.00">
                        </div>
                        <input type="number" name="vehicle_type_durations[{{ $vehicleType->id }}]"
                            value="{{ $currentDuration }}" step="1" min="1"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-center text-sm"
                            placeholder="Min">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
