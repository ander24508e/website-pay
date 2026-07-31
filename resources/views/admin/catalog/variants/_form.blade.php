@php
    $currentCost = (float) old('cost_price', $catalogVariant?->cost_price ?? 0);
    $currentPrice = (float) old('price', $catalogVariant?->price ?? 0);
    $computedMargin = $currentCost > 0 ? round((($currentPrice - $currentCost) / $currentCost) * 100, 2) : 40;
    $selectedMargin = (string) old('profit_margin_percentage', $computedMargin);
    $presetMargins = ['20', '30', '40', '50'];
    $isCustomMargin = !in_array($selectedMargin, $presetMargins, true);
    $presentationPresets = ['Unidad', '250 ml', '500 ml', '800 ml', '1 litro', 'Galon', 'Caneca', 'Caja', 'Paquete'];
    $inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
    $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
@endphp

<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">
    <div class="space-y-4">
        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Producto padre</p>
            <label class="{{ $labelClass }}">Producto *</label>
            <select name="catalog_item_id" id="presentation_item_id"
                class="{{ $inputClass }} bg-white @error('catalog_item_id') border-red-400 bg-red-50 @enderror">
                @if ($showEmptyItemOption)
                    <option value="">Selecciona un producto</option>
                @endif
                @foreach($items as $item)
                    @php
                        $prefixSource = $item->type?->slug ?: $item->type?->name ?: $item->name;
                        $prefix = \Illuminate\Support\Str::substr(str_replace('-', '', \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($prefixSource))), 0, 4) ?: 'PRES';
                    @endphp
                    <option value="{{ $item->id }}" data-prefix="{{ $prefix }}"
                        {{ old('catalog_item_id', $selectedItemId ?? $catalogVariant?->catalog_item_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->type?->name ?? 'Seccion' }} / {{ $item->name }}
                    </option>
                @endforeach
            </select>
            @error('catalog_item_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Presentacion</p>
            <p class="mb-3 text-xs text-gray-500">Define como se vendera o contara este producto.</p>

            <label class="{{ $labelClass }}">Presentacion *</label>
            <input type="text" name="name" id="presentation_name"
                value="{{ old('name', $catalogVariant?->name) }}"
                class="{{ $inputClass }} bg-white @error('name') border-red-400 bg-red-50 @enderror"
                placeholder="Ej: 1 litro, Galon, Caneca">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($presentationPresets as $preset)
                    <button type="button" data-presentation-preset="{{ $preset }}"
                        class="inline-flex h-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                        {{ $preset }}
                    </button>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Precios</p>
            <p class="mb-3 text-xs text-gray-500">El precio de venta se calcula desde costo y ganancia.</p>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Precio de compra</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm font-semibold text-gray-400">$</span>
                        <input type="number" name="cost_price" id="presentation_cost_price"
                            value="{{ old('cost_price', $catalogVariant?->cost_price) }}" step="0.01" min="0"
                            class="{{ $inputClass }} bg-white pl-8 @error('cost_price') border-red-400 bg-red-50 @enderror"
                            placeholder="0.00">
                    </div>
                    @error('cost_price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Ganancia</label>
                    <select id="presentation_margin_select"
                        class="{{ $inputClass }} bg-white @error('profit_margin_percentage') border-red-400 bg-red-50 @enderror">
                        @foreach ($presetMargins as $margin)
                            <option value="{{ $margin }}" {{ !$isCustomMargin && $selectedMargin === $margin ? 'selected' : '' }}>
                                {{ $margin }}%
                            </option>
                        @endforeach
                        <option value="custom" {{ $isCustomMargin ? 'selected' : '' }}>Personalizado</option>
                    </select>
                    <div id="presentation_margin_custom_wrap" class="{{ $isCustomMargin ? '' : 'hidden' }} mt-2">
                        <div class="relative">
                            <input type="number" id="presentation_margin_custom"
                                value="{{ $isCustomMargin ? $selectedMargin : '' }}" step="0.01" min="0"
                                class="{{ $inputClass }} bg-white pr-8" placeholder="Ingresa porcentaje">
                            <span class="absolute right-3 top-2.5 text-sm font-semibold text-gray-400">%</span>
                        </div>
                    </div>
                    <input type="hidden" name="profit_margin_percentage" id="presentation_margin" value="{{ $selectedMargin }}">
                    @error('profit_margin_percentage')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}">Precio de venta</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm font-semibold text-gray-400">$</span>
                        <input type="number" name="price" id="presentation_sale_price"
                            value="{{ old('price', $catalogVariant?->price) }}" step="0.01" min="0"
                            class="{{ $inputClass }} bg-white pl-8 text-gray-500 @error('price') border-red-400 bg-red-50 @enderror"
                            placeholder="0.00" readonly>
                    </div>
                    @error('price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="space-y-4">
        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Inventario</p>
            <p class="mb-3 text-xs text-gray-500">Cada presentacion tiene su propio SKU y stock.</p>

            <div class="space-y-3">
                <div>
                    <label class="{{ $labelClass }}">SKU automatico</label>
                    <input type="text" name="sku" id="presentation_sku"
                        value="{{ old('sku', $catalogVariant?->sku) }}"
                        class="{{ $inputClass }} bg-white text-gray-500 @error('sku') border-red-400 bg-red-50 @enderror"
                        readonly>
                    @error('sku')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Stock inicial</label>
                    <input type="number" name="stock" value="{{ old('stock', $catalogVariant?->stock) }}" min="0"
                        class="{{ $inputClass }} bg-white @error('stock') border-red-400 bg-red-50 @enderror"
                        placeholder="0">
                    @error('stock')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClass }}">Stock minimo</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $catalogVariant?->min_stock ?? 0) }}" min="0"
                        class="{{ $inputClass }} bg-white @error('min_stock') border-red-400 bg-red-50 @enderror"
                        placeholder="0">
                    @error('min_stock')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-gray-100 bg-gray-50 p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Estado</p>
            <div class="space-y-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                    <input type="checkbox" name="active" value="1" {{ old('active', $catalogVariant?->active ?? true) ? 'checked' : '' }}
                        class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <span>
                        <span class="block text-sm font-semibold text-gray-700">Activa</span>
                        <span class="block text-xs text-gray-400">Disponible para ventas e inventario</span>
                    </span>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $catalogVariant?->is_default ?? false) ? 'checked' : '' }}
                        class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <span>
                        <span class="block text-sm font-semibold text-gray-700">Principal</span>
                        <span class="block text-xs text-gray-400">Se selecciona primero en web y ventas</span>
                    </span>
                </label>
            </div>
        </section>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const skuInput = document.getElementById('presentation_sku');
                const itemSelect = document.getElementById('presentation_item_id');
                const costInput = document.getElementById('presentation_cost_price');
                const marginSelect = document.getElementById('presentation_margin_select');
                const marginCustomWrap = document.getElementById('presentation_margin_custom_wrap');
                const marginCustomInput = document.getElementById('presentation_margin_custom');
                const marginInput = document.getElementById('presentation_margin');
                const saleInput = document.getElementById('presentation_sale_price');
                const nameInput = document.getElementById('presentation_name');

                function randomSku() {
                    const selected = itemSelect?.selectedOptions?.[0];
                    const prefix = selected?.dataset?.prefix || 'PRES';
                    const date = new Date();
                    const ymd = String(date.getFullYear()).slice(-2) +
                        String(date.getMonth() + 1).padStart(2, '0') +
                        String(date.getDate()).padStart(2, '0');
                    const token = Math.random().toString(36).substring(2, 7).toUpperCase();

                    return `${prefix}-${ymd}-${token}`;
                }

                function calculatePrice() {
                    const cost = parseFloat(costInput?.value || '0');
                    const margin = parseFloat(marginInput?.value || '0');

                    if (!saleInput) return;

                    if (cost > 0) {
                        const sale = cost + (cost * margin / 100);
                        saleInput.value = sale.toFixed(2);
                    } else if (!saleInput.value) {
                        saleInput.value = '0.00';
                    }
                }

                function syncMargin() {
                    if (!marginSelect || !marginInput) return;

                    if (marginSelect.value === 'custom') {
                        marginCustomWrap?.classList.remove('hidden');
                        marginInput.value = marginCustomInput?.value || 0;
                    } else {
                        marginCustomWrap?.classList.add('hidden');
                        marginInput.value = marginSelect.value;
                    }

                    calculatePrice();
                }

                if (skuInput && !skuInput.value) {
                    skuInput.value = randomSku();
                }

                itemSelect?.addEventListener('change', () => {
                    if (skuInput) skuInput.value = randomSku();
                });

                document.querySelectorAll('[data-presentation-preset]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (nameInput) nameInput.value = button.dataset.presentationPreset || '';
                        nameInput?.focus();
                    });
                });

                costInput?.addEventListener('input', calculatePrice);
                marginSelect?.addEventListener('change', syncMargin);
                marginCustomInput?.addEventListener('input', syncMargin);
                syncMargin();
                calculatePrice();
            });
        </script>
    @endpush
@endonce
