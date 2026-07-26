@php
    $savedSupplies = isset($catalogItem) ? $catalogItem->supplies : collect();
    $oldSupplies = collect(old('supplies', []));
    $rows = $oldSupplies->isNotEmpty()
        ? $oldSupplies
        : ($savedSupplies->isNotEmpty()
            ? $savedSupplies->map(fn ($supply) => [
                'catalog_item_variant_id' => $supply->catalog_item_variant_id,
                'quantity' => $supply->quantity,
                'unit' => $supply->unit,
            ])
            : collect([
                ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
                ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
                ['catalog_item_variant_id' => '', 'quantity' => '', 'unit' => ''],
            ]));
@endphp

<section class="mt-8 border-t border-gray-100 pt-6 space-y-4">
    <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Insumos consumidos</p>
        <p class="mt-1 text-sm text-gray-500">
            Relaciona este servicio con productos inventariables para descontarlos cuando se venda.
        </p>
    </div>

    @if ($supplyVariants->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
            No hay productos inventariables disponibles para usar como insumos.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($rows as $index => $row)
                <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 sm:grid-cols-[minmax(0,1fr)_120px_120px]">
                    <select name="supplies[{{ $index }}][catalog_item_variant_id]"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm">
                        <option value="">Selecciona un insumo</option>
                        @foreach ($supplyVariants as $variant)
                            <option value="{{ $variant->id }}" {{ (string) data_get($row, 'catalog_item_variant_id') === (string) $variant->id ? 'selected' : '' }}>
                                {{ $variant->item?->name }} / {{ $variant->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="supplies[{{ $index }}][quantity]"
                        value="{{ data_get($row, 'quantity') }}" step="0.001" min="0.001"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-center text-sm"
                        placeholder="Cantidad">
                    <input type="text" name="supplies[{{ $index }}][unit]"
                        value="{{ data_get($row, 'unit') }}"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-center text-sm"
                        placeholder="Unidad">
                </div>
            @endforeach
        </div>
    @endif
</section>
