@php
    $savedVehicleTypePrices = isset($catalogItem)
        ? $catalogItem->vehicleTypePrices->pluck('price', 'vehicle_type_id')
        : collect();
@endphp

<section class="mt-8 border-t border-gray-100 pt-6 space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Precios por tipo de vehiculo</p>
            <p class="text-sm text-gray-500 mt-1">
                Configura solo los tipos atendidos. Si un precio queda vacío, se utilizará el precio base del servicio.
            </p>
        </div>
        @if (!empty($quickVehicleModalAvailable))
            <button type="button" data-open-vehicle-modal
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-700">
                Crear especificaciones
            </button>
        @endif
    </div>

    @if ($vehicleTypes->isEmpty())
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            @if (empty($quickVehicleModalAvailable))
                <span>Aún no existen tipos de vehículo.</span>
                <a href="{{ route('admin.vehiculos.create') }}"
                    class="ml-1 font-semibold underline">
                    Crear vehículo
                </a>
            @else
                Aún no existen tipos de vehículo. Usa el botón Crear especificaciones para agregar uno.
            @endif
        </div>
    @else
        <div class="rounded-xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-[minmax(0,1fr)_140px] bg-gray-50 border-b border-gray-200 px-4 py-3 text-xs font-semibold uppercase text-gray-500">
                <span>Tipo de vehiculo</span>
                <span class="text-center">Precio</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($vehicleTypes as $vehicleType)
                    @php
                        $fieldName = 'vehicle_type_prices.' . $vehicleType->id;
                        $currentPrice = old($fieldName, $savedVehicleTypePrices->get($vehicleType->id));
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_140px] gap-3 sm:items-center px-4 py-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800">{{ $vehicleType->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $vehicleType->description ?: 'Sin descripcion adicional.' }}</p>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" name="vehicle_type_prices[{{ $vehicleType->id }}]"
                                value="{{ $currentPrice }}" step="0.01" min="0"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pl-7 pr-3 text-sm text-center"
                                placeholder="Base">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
