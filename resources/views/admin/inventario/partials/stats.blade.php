<div class="grid grid-cols-2 md:grid-cols-4 2xl:grid-cols-8 gap-3 sm:gap-4 mb-6">
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Presentaciones</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['variants'] }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Unidades</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['units'] }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Valor</p>
        <p class="text-2xl font-bold text-gray-900 mt-2 break-words">
            {{ $canViewCosts ? '$' . number_format($inventoryStats['value'], 2) : 'Restringido' }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Agotados</p>
        <p class="text-2xl font-bold text-red-700 mt-2">{{ $inventoryStats['out'] }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Bajo stock</p>
        <p class="text-2xl font-bold text-amber-700 mt-2">{{ $inventoryStats['low'] }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Sin costo</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['no_cost'] }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Sin SKU</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['missing_sku'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
        <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Revisar</p>
        <p class="text-2xl font-bold text-amber-700 mt-2">{{ $inventoryStats['mismatches'] ?? 0 }}</p>
    </div>
</div>
