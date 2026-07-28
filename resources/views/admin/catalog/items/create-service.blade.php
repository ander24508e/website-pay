@extends('layouts.admin')

@section('title', 'Nuevo Servicio')

@section('content')
@php
    $itemSingular = 'Servicio';
    $itemPlural = 'servicios';
    $returnToCategory = $returnToCategory ?? false;
    $returnUrl = ($returnToCategory && $selectedCategoryId > 0)
        ? route('admin.catalog-items.index', [
            'catalog_type_id' => $selectedTypeId,
            'catalog_category_id' => $selectedCategoryId,
        ])
        : (($returnToType && $selectedTypeId > 0)
        ? route('admin.catalog-types.show', $selectedTypeId)
        : ($selectedTypeId > 0 ? route('admin.catalog-items.index', ['catalog_type_id' => $selectedTypeId]) : route('admin.catalog.index')));
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
            <h2 class="text-xl font-bold leading-tight text-gray-900 sm:text-2xl">Nuevo Servicio</h2>
            <p class="text-sm text-gray-400">Crea un servicio para mostrarlo en la web y usarlo en ventas.</p>
        </div>
    </div>

    <section class="rounded-lg bg-white shadow-sm xl:flex xl:h-[calc(100%-4.25rem)] xl:flex-col xl:overflow-hidden">
        @if($types->isEmpty())
            <div class="p-4">
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    Primero necesitas crear al menos un negocio de servicios antes de registrar {{ $itemPlural }}.
                    <div class="mt-4">
                        <a href="{{ route('admin.catalog-types.create') }}"
                            class="inline-flex justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">
                            Crear Sección
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form id="catalog-item-form" action="{{ route('admin.catalog-items.store') }}" method="POST" enctype="multipart/form-data"
                class="xl:flex xl:h-full xl:flex-col xl:overflow-hidden">
                @csrf
                <input type="hidden" name="redirect_to_type" value="{{ $returnToType ? 1 : 0 }}">
                <input type="hidden" name="redirect_to_category" value="{{ $returnToCategory ? 1 : 0 }}">

                <div class="grid gap-4 p-4 xl:grid-cols-[minmax(0,1fr)_340px] xl:overflow-y-auto">
                    <div class="space-y-4 min-w-0">
                        <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Imagen</p>
                            <div class="grid gap-3 sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center">
                                <div id="img-preview"
                                    class="mx-auto flex h-28 w-28 items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-200 bg-white text-center text-xs text-gray-400 sm:h-32 sm:w-32">
                                    Sin imagen
                                </div>
                                <div class="min-w-0">
                                    <p class="mb-2 truncate text-xs text-gray-400" id="img-name">Sin imagen seleccionada</p>
                                    <input type="file" name="image" id="image-input" accept="image/*" class="hidden" onchange="previewImage(this)">
                                    <button type="button" onclick="document.getElementById('image-input').click()"
                                        class="w-full rounded-lg bg-gray-900 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 sm:max-w-xs">
                                        Subir Imagen
                                    </button>
                                    <p class="mt-2 text-xs text-gray-400">JPG, PNG o WEBP - Máx. 6MB</p>
                                    @error('image')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Información básica</p>
                                <p class="text-xs text-gray-500">Nombre, ubicación dentro del catálogo y precio principal.</p>
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">Nombre *</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="{{ $inputClass }} @error('name') border-red-400 bg-red-50 @enderror"
                                    placeholder="Ej: Lavado premium, mantenimiento, asesoría">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="{{ $labelClass }}">Negocio *</label>
                                    <select name="catalog_type_id" id="catalog_type_id"
                                        class="{{ $inputClass }} @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                                        @unless($returnToType && $selectedTypeId > 0)
                                            <option value="">Selecciona un negocio</option>
                                        @endunless
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ (old('catalog_type_id', $selectedTypeId ?: null) == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('catalog_type_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Categoría</label>
                                    <select name="catalog_category_id" id="catalog_category_id"
                                        class="{{ $inputClass }} @error('catalog_category_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-type="{{ $category->catalog_type_id }}" {{ (old('catalog_category_id', $selectedCategoryId ?: null) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->type->name }} / {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('catalog_category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="{{ $labelClass }}">Precio base</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-sm font-semibold text-gray-400">$</span>
                                        <input type="number" name="base_price" value="{{ old('base_price') }}" step="0.01" min="0"
                                            class="{{ $inputClass }} pl-8 @error('base_price') border-red-400 bg-red-50 @enderror"
                                            placeholder="0.00">
                                    </div>
                                    @error('base_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="{{ $labelClass }}">Duración base</label>
                                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" step="1"
                                        class="{{ $inputClass }} @error('duration_minutes') border-red-400 bg-red-50 @enderror"
                                        placeholder="Ej: 45">
                                    @error('duration_minutes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="{{ $labelClass }}">Descripción</label>
                                <textarea name="description" rows="3"
                                    class="{{ $inputClass }} resize-none @error('description') border-red-400 bg-red-50 @enderror"
                                    placeholder="Describe este servicio y para qué sirve dentro del negocio">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-4">
                        <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Cómo se usará</p>
                            <p class="mb-3 text-xs text-gray-500">Este negocio está configurado como servicio, por eso no maneja stock.</p>
                            <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                    <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Activo</span>
                                        <span class="block text-xs text-gray-400">Visible en catálogo</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                    <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Destacado</span>
                                        <span class="block text-xs text-gray-400">Mayor prioridad</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3 sm:col-span-2 xl:col-span-1">
                                    <input type="checkbox" name="reservable" value="1" {{ old('reservable') ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Se puede reservar</span>
                                        <span class="block text-xs text-gray-400">Puede usarse para reservas</span>
                                    </span>
                                </label>
                            </div>
                        </section>

                        <details class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">Precios por vehículo</summary>
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

                        <details class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">Opciones avanzadas</summary>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                                <div>
                                    <label class="{{ $labelClass }}">Slug</label>
                                    <input type="text" name="slug" value="{{ old('slug') }}"
                                        class="{{ $inputClass }} bg-white @error('slug') border-red-400 bg-red-50 @enderror"
                                        placeholder="Se genera automáticamente">
                                    @error('slug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Orden</label>
                                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                        class="{{ $inputClass }} bg-white @error('sort_order') border-red-400 bg-red-50 @enderror">
                                    @error('sort_order') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </details>
                    </aside>
                </div>

                <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                    <a href="{{ $returnUrl }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                        Guardar Servicio
                    </button>
                </div>
            </form>

            @include('admin.vehiculos.partials.specifications-modal-types-only', ['modalId' => 'serviceVehicleTypesModal'])
        @endif
    </section>
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
        preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
    };
    reader.readAsDataURL(file);
    document.getElementById('img-name').textContent = file.name;
}

(function filterCategoriesByType() {
    const typeSelect = document.getElementById('catalog_type_id');
    const categorySelect = document.getElementById('catalog_category_id');
    if (!typeSelect || !categorySelect) return;

    const allOptions = Array.from(categorySelect.querySelectorAll('option'));

    function renderOptions() {
        const selectedType = typeSelect.value;
        const currentValue = categorySelect.value;

        categorySelect.innerHTML = '';

        allOptions.forEach(option => {
            if (!option.value || !selectedType || option.dataset.type === selectedType) {
                categorySelect.appendChild(option.cloneNode(true));
            }
        });

        if ([...categorySelect.options].some(option => option.value === currentValue)) {
            categorySelect.value = currentValue;
        }
    }

    typeSelect.addEventListener('change', renderOptions);
    renderOptions();
})();
</script>
@endpush
