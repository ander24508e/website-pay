@extends('layouts.admin')

@section('title', 'Editar Producto o Servicio')

@section('content')
@php
    $selectedTypeId = (int) old('catalog_type_id', $catalogItem->catalog_type_id);
    $currentType = $types->firstWhere('id', $selectedTypeId) ?? $catalogItem->type;
    $isProductContext = $currentType && (($currentType->business_model ?? 'services') === \App\Models\CatalogType::BUSINESS_MODEL_PRODUCTS);
    $itemSingular = $currentType ? ($isProductContext ? 'Producto' : 'Servicio') : 'Producto o Servicio';
    $returnUrl = $returnUrl ?? route('admin.catalog-items.show', $catalogItem);
    $returnContext = $returnContext ?? [];
    $defaultVariant = $catalogItem->variants->firstWhere('is_default', true) ?? $catalogItem->variants->first();
    $currentCost = (float) old('variant_cost_price', $defaultVariant?->cost_price ?? 0);
    $currentSalePrice = (float) old('base_price', $catalogItem->base_price ?? 0);
    $computedMargin = $currentCost > 0 ? round((($currentSalePrice - $currentCost) / $currentCost) * 100, 2) : 40;
    $selectedMargin = (string) old('profit_margin_percentage', $computedMargin);
    $presetMargins = ['20', '30', '40', '50'];
    $isCustomMargin = !in_array($selectedMargin, $presetMargins, true);
    $inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
    $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
@endphp

<div class="mx-auto w-full max-w-[1500px] px-3 pb-4 sm:px-5 xl:h-[calc(100vh-2rem)] xl:overflow-hidden">
    <div class="mb-3 flex items-center gap-3">
        <a href="{{ $returnUrl }}"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="min-w-0">
            <h2 class="text-xl font-bold leading-tight text-gray-900 sm:text-2xl">Editar {{ $itemSingular }}</h2>
            <p class="truncate text-sm text-gray-400">Modificando <strong class="text-gray-600">{{ $catalogItem->name }}</strong></p>
        </div>
    </div>

    <form id="catalog-item-form" action="{{ route('admin.catalog-items.update', $catalogItem) }}" method="POST" enctype="multipart/form-data"
        class="rounded-lg bg-white shadow-sm xl:flex xl:h-[calc(100%-4.25rem)] xl:flex-col xl:overflow-hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="slug" value="">
        <input type="hidden" name="sort_order" value="{{ old('sort_order', $catalogItem->sort_order) }}">
        <input type="hidden" name="base_price" id="base_price" value="{{ old('base_price', $catalogItem->base_price) }}">
        <input type="hidden" name="new_category_name" id="new_category_name" value="{{ old('new_category_name') }}">
        <input type="hidden" name="new_category_description" id="new_category_description" value="{{ old('new_category_description') }}">
        <input type="hidden" name="redirect_to_inventory" value="{{ !empty($returnContext['from_inventory']) ? 1 : 0 }}">
        <input type="hidden" name="redirect_to_type" value="{{ !empty($returnContext['return_to_type']) ? 1 : 0 }}">
        <input type="hidden" name="redirect_to_category" value="{{ !empty($returnContext['return_to_category']) ? 1 : 0 }}">
        <input type="hidden" name="redirect_to_items" value="{{ !empty($returnContext['return_to_items']) ? 1 : 0 }}">
        @if ($isProductContext)
            <input type="hidden" name="catalog_type_id" id="catalog_type_id" value="{{ $selectedTypeId }}">
        @endif

        <div class="grid gap-4 p-4 xl:grid-cols-[minmax(0,1fr)_340px] xl:overflow-y-auto">
            <div class="min-w-0 space-y-4">
                <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Imagen</p>
                    <div class="grid gap-3 sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center">
                        <div id="img-preview"
                            class="mx-auto flex h-28 w-28 items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-200 bg-white text-center text-xs text-gray-400 sm:h-32 sm:w-32">
                            @if ($catalogItem->image)
                                <img src="{{ $catalogItem->image_url }}" class="h-full w-full object-cover" alt="{{ $catalogItem->name }}">
                            @else
                                Sin imagen
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="mb-2 truncate text-xs text-gray-400" id="img-name">{{ basename((string) $catalogItem->image) ?: 'Sin imagen seleccionada' }}</p>
                            <input type="file" name="image" id="image-input" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <button type="button" onclick="document.getElementById('image-input').click()"
                                class="w-full rounded-lg bg-gray-900 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 sm:max-w-xs">
                                Cambiar Imagen
                            </button>
                            <p class="mt-2 text-xs text-gray-400">JPG, PNG o WEBP - Max. 6MB</p>
                            @error('image')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Informacion basica</p>
                        <p class="text-xs text-gray-500">
                            {{ $isProductContext ? 'Nombre, categoria, costo, margen y descripcion.' : 'Nombre, seccion, categoria, precio y descripcion.' }}
                        </p>
                    </div>

                    <div>
                        <label class="{{ $labelClass }}">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $catalogItem->name) }}"
                            class="{{ $inputClass }} @error('name') border-red-400 bg-red-50 @enderror">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @unless ($isProductContext)
                        <div>
                            <label class="{{ $labelClass }}">Seccion *</label>
                            <select name="catalog_type_id" id="catalog_type_id"
                                class="{{ $inputClass }} @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('catalog_type_id', $catalogItem->catalog_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('catalog_type_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    @endunless

                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold text-gray-700">Categoria</label>
                            @if ($isProductContext)
                                <button type="button" data-open-category-modal
                                    class="inline-flex h-8 items-center justify-center rounded-lg bg-gray-100 px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-200">
                                    + Nueva categoria
                                </button>
                            @endif
                        </div>
                        <select name="catalog_category_id" id="catalog_category_id"
                            class="{{ $inputClass }} @error('catalog_category_id') border-red-400 bg-red-50 @enderror">
                            <option value="">Sin categoria</option>
                            @foreach($categories as $category)
                                @if (!$isProductContext || (int) $category->catalog_type_id === $selectedTypeId)
                                    <option value="{{ $category->id }}" data-type="{{ $category->catalog_type_id }}"
                                        {{ old('catalog_category_id', $catalogItem->catalog_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $isProductContext ? $category->name : ($category->type->name . ' / ' . $category->name) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @if ($isProductContext)
                            <p id="new-category-preview"
                                class="mt-1 {{ old('new_category_name') ? '' : 'hidden' }} text-xs text-gray-500">
                                @if (old('new_category_name'))
                                    Nueva categoria: {{ old('new_category_name') }}
                                @endif
                            </p>
                        @endif
                        @error('catalog_category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('new_category_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @if ($isProductContext)
                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_220px]">
                            <div>
                                <label class="{{ $labelClass }}">Precio de compra</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-sm font-semibold text-gray-400">$</span>
                                    <input type="number" name="variant_cost_price" id="purchase_price"
                                        value="{{ old('variant_cost_price', $defaultVariant?->cost_price) }}" step="0.01" min="0"
                                        class="{{ $inputClass }} pl-8 @error('variant_cost_price') border-red-400 bg-red-50 @enderror"
                                        placeholder="0.00">
                                </div>
                                @error('variant_cost_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">Ganancia</label>
                                <select id="profit_margin_select"
                                    class="{{ $inputClass }} @error('profit_margin_percentage') border-red-400 bg-red-50 @enderror">
                                    @foreach ($presetMargins as $margin)
                                        <option value="{{ $margin }}" {{ !$isCustomMargin && $selectedMargin === $margin ? 'selected' : '' }}>
                                            {{ $margin }}%
                                        </option>
                                    @endforeach
                                    <option value="custom" {{ $isCustomMargin ? 'selected' : '' }}>Personalizado</option>
                                </select>
                                <div id="profit_margin_custom_wrap" class="{{ $isCustomMargin ? '' : 'hidden' }} mt-2">
                                    <div class="relative">
                                        <input type="number" id="profit_margin_custom"
                                            value="{{ $isCustomMargin ? $selectedMargin : '' }}" step="0.01" min="0"
                                            class="{{ $inputClass }} pr-8" placeholder="Ingresa porcentaje">
                                        <span class="absolute right-3 top-2 text-sm font-semibold text-gray-400">%</span>
                                    </div>
                                </div>
                                <input type="hidden" name="profit_margin_percentage" id="profit_margin_percentage" value="{{ $selectedMargin }}">
                                @error('profit_margin_percentage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @else
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="{{ $labelClass }}">Precio base</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-sm font-semibold text-gray-400">$</span>
                                    <input type="number" name="base_price" value="{{ old('base_price', $catalogItem->base_price) }}" step="0.01" min="0"
                                        class="{{ $inputClass }} pl-8 @error('base_price') border-red-400 bg-red-50 @enderror">
                                </div>
                                @error('base_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">Duracion base</label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $catalogItem->duration_minutes) }}" min="1" step="1"
                                    class="{{ $inputClass }} @error('duration_minutes') border-red-400 bg-red-50 @enderror"
                                    placeholder="Ej: 45">
                                @error('duration_minutes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="{{ $labelClass }}">Descripcion</label>
                        <textarea name="description" rows="3"
                            class="{{ $inputClass }} resize-none @error('description') border-red-400 bg-red-50 @enderror">{{ old('description', $catalogItem->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </section>
            </div>

            <aside class="space-y-4">
                @if ($isProductContext)
                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Inventario</p>
                        <p class="mb-3 text-xs text-gray-500">El SKU y el precio de venta se mantienen sincronizados.</p>
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                            <div>
                                <label class="{{ $labelClass }}">SKU automatico</label>
                                <input type="text" name="variant_sku" id="variant_sku"
                                    value="{{ old('variant_sku', $defaultVariant?->sku) }}"
                                    class="{{ $inputClass }} bg-white text-gray-500 @error('variant_sku') border-red-400 bg-red-50 @enderror"
                                    readonly>
                                @error('variant_sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Precio de venta</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-sm font-semibold text-gray-400">$</span>
                                    <input type="number" id="sale_price_display" value="{{ old('base_price', $catalogItem->base_price) }}"
                                        step="0.01" min="0" class="{{ $inputClass }} bg-white pl-8 text-gray-500"
                                        placeholder="0.00" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Stock actual</label>
                                <input type="number" value="{{ $defaultVariant?->stock ?? 0 }}"
                                    class="{{ $inputClass }} bg-white text-gray-500" readonly>
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Stock minimo</label>
                                <input type="number" name="variant_min_stock" value="{{ old('variant_min_stock', $defaultVariant?->min_stock ?? 0) }}"
                                    min="0"
                                    class="{{ $inputClass }} bg-white @error('variant_min_stock') border-red-400 bg-red-50 @enderror">
                                @error('variant_min_stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Estado</p>
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                            <input type="checkbox" name="active" value="1" {{ old('active', $catalogItem->active) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                            <span>
                                <span class="block text-sm font-semibold text-gray-700">Activo</span>
                                <span class="block text-xs text-gray-400">Visible en catalogo</span>
                            </span>
                        </label>
                    </section>
                @else
                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Como se usa</p>
                        <p class="mb-3 text-xs text-gray-500">El modelo del negocio define el comportamiento del servicio.</p>
                        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                <input type="checkbox" name="active" value="1" {{ old('active', $catalogItem->active) ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Activo</span>
                                    <span class="block text-xs text-gray-400">Visible en catalogo</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                <input type="checkbox" name="featured" value="1" {{ old('featured', $catalogItem->featured) ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Destacado</span>
                                    <span class="block text-xs text-gray-400">Mayor prioridad</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3 sm:col-span-2 xl:col-span-1">
                                <input type="checkbox" name="reservable" value="1" {{ old('reservable', $catalogItem->reservable) ? 'checked' : '' }}
                                    class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Se puede reservar</span>
                                    <span class="block text-xs text-gray-400">Puede usarse para reservas</span>
                                </span>
                            </label>
                        </div>
                    </section>

                    <details class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700">Precios por vehiculo</summary>
                        <div class="mt-3">
                            @include('admin.catalog.items._vehicle-type-prices', [
                                'quickVehicleModalAvailable' => true,
                                'vehicleModalId' => 'serviceVehicleTypesModal',
                            ])
                        </div>
                    </details>

                    <details class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700">Insumos del servicio</summary>
                        <div class="mt-3">
                            @include('admin.catalog.items._supplies')
                        </div>
                    </details>
                @endif
            </aside>
        </div>

        <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
            <a href="{{ $returnUrl }}"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                Cancelar
            </a>
            <button type="submit"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                Actualizar {{ $itemSingular }}
            </button>
        </div>
    </form>

    @if ($isProductContext)
        <div id="categoryModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-gray-900/50" data-close-category-modal></div>
            <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="font-bold text-gray-900">Nueva categoria</h3>
                    <p class="mt-1 text-sm text-gray-500">Se creara dentro del negocio actual.</p>
                </div>
                <div class="space-y-3 p-5">
                    <div>
                        <label class="{{ $labelClass }}">Nombre *</label>
                        <input type="text" id="modal_category_name" value="{{ old('new_category_name') }}"
                            class="{{ $inputClass }}" placeholder="Ej: Interior vehiculo">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Descripcion</label>
                        <textarea id="modal_category_description" rows="3" class="{{ $inputClass }} resize-none" placeholder="Opcional">{{ old('new_category_description') }}</textarea>
                    </div>
                </div>
                <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                    <button type="button" data-close-category-modal
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="button" id="applyCategory"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                        Usar categoria
                    </button>
                </div>
            </div>
        </div>
    @else
        @include('admin.vehiculos.partials.specifications-modal-types-only', ['modalId' => 'serviceVehicleTypesModal'])
    @endif
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    if (file.size > 6 * 1024 * 1024) {
        alert('La imagen no debe superar 6MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('img-preview');
        preview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" alt="">`;
    };
    reader.readAsDataURL(file);
    document.getElementById('img-name').textContent = file.name;
}

function calculateSalePrice() {
    const costInput = document.getElementById('purchase_price');
    const marginInput = document.getElementById('profit_margin_percentage');
    const saleDisplay = document.getElementById('sale_price_display');
    const basePrice = document.getElementById('base_price');
    const cost = parseFloat(costInput?.value || '0');
    const margin = parseFloat(marginInput?.value || '0');
    const sale = cost > 0 ? cost + (cost * margin / 100) : 0;
    const value = sale.toFixed(2);

    if (saleDisplay) saleDisplay.value = value;
    if (basePrice) basePrice.value = value;
}

function syncMarginValue() {
    const select = document.getElementById('profit_margin_select');
    const customWrap = document.getElementById('profit_margin_custom_wrap');
    const customInput = document.getElementById('profit_margin_custom');
    const marginInput = document.getElementById('profit_margin_percentage');

    if (!select || !marginInput) return;

    if (select.value === 'custom') {
        customWrap?.classList.remove('hidden');
        marginInput.value = customInput?.value || 0;
    } else {
        customWrap?.classList.add('hidden');
        marginInput.value = select.value;
    }

    calculateSalePrice();
}

(function bootCatalogEditForm() {
    const typeSelect = document.getElementById('catalog_type_id');
    const categorySelect = document.getElementById('catalog_category_id');

    if (typeSelect && categorySelect && typeSelect.tagName === 'SELECT') {
        const allOptions = Array.from(categorySelect.querySelectorAll('option'));

        function renderOptions() {
            const selectedType = typeSelect.value;
            const currentValue = categorySelect.value;

            categorySelect.innerHTML = '';

            allOptions.forEach((option, index) => {
                if (index === 0) {
                    categorySelect.appendChild(option.cloneNode(true));
                    return;
                }

                if (!selectedType || option.dataset.type === selectedType) {
                    const clone = option.cloneNode(true);
                    if (clone.value === currentValue) clone.selected = true;
                    categorySelect.appendChild(clone);
                }
            });
        }

        typeSelect.addEventListener('change', renderOptions);
        renderOptions();
    }

    document.getElementById('purchase_price')?.addEventListener('input', calculateSalePrice);
    document.getElementById('profit_margin_select')?.addEventListener('change', syncMarginValue);
    document.getElementById('profit_margin_custom')?.addEventListener('input', syncMarginValue);
    syncMarginValue();

    const modal = document.getElementById('categoryModal');
    const nameInput = document.getElementById('modal_category_name');
    const descriptionInput = document.getElementById('modal_category_description');
    const hiddenName = document.getElementById('new_category_name');
    const hiddenDescription = document.getElementById('new_category_description');
    const preview = document.getElementById('new-category-preview');

    function openModal() {
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
        nameInput?.focus();
    }

    function closeModal() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
    }

    document.querySelector('[data-open-category-modal]')?.addEventListener('click', openModal);
    modal?.querySelectorAll('[data-close-category-modal]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.getElementById('applyCategory')?.addEventListener('click', () => {
        const name = nameInput.value.trim();
        if (!name) {
            nameInput.focus();
            return;
        }

        hiddenName.value = name;
        hiddenDescription.value = descriptionInput.value.trim();
        if (categorySelect) categorySelect.value = '';
        if (preview) {
            preview.textContent = `Nueva categoria: ${name}`;
            preview.classList.remove('hidden');
        }
        closeModal();
    });

    categorySelect?.addEventListener('change', () => {
        if (categorySelect.value && hiddenName) {
            hiddenName.value = '';
            hiddenDescription.value = '';
            if (preview) {
                preview.textContent = '';
                preview.classList.add('hidden');
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
})();
</script>
@endpush
