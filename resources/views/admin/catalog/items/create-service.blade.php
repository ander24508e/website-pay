@extends('layouts.admin')

@section('title', 'Nuevo Servicio')

@section('content')
@php
    $itemSingular = 'Servicio';
    $itemPlural = 'servicios';
@endphp

<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ ($returnToType && $selectedTypeId > 0) ? route('admin.catalog-types.show', $selectedTypeId) : ($selectedTypeId > 0 ? route('admin.catalog-items.index', ['catalog_type_id' => $selectedTypeId]) : route('admin.catalog.index')) }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Nuevo Servicio</h2>
            <p class="text-gray-400 text-sm">Crea un servicio para este negocio.</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen</p>

                <div class="flex flex-col items-center mb-4">
                    <div id="img-preview"
                         class="w-28 h-28 sm:w-32 sm:h-32 bg-gray-100 rounded-xl flex items-center justify-center text-sm text-gray-400 mb-3 overflow-hidden border-2 border-dashed border-gray-200">
                        Sin imagen
                    </div>
                    <p class="text-xs text-gray-400 text-center" id="img-name">Sin imagen seleccionada</p>
                </div>

                <input type="file" name="image" id="image-input" accept="image/*"
                       form="catalog-item-form" class="hidden" onchange="previewImage(this)">

                <button type="button" onclick="document.getElementById('image-input').click()"
                        class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    Subir Imagen
                </button>
                <p class="text-xs text-gray-400 text-center mt-2">JPG, PNG o WEBP - Max. 6MB</p>

                @error('image')
                    <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                @if($types->isEmpty())
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                        Primero necesitas crear al menos un negocio de servicios antes de registrar {{ $itemPlural }}.
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('admin.catalog-types.create') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                            Crear Sección
                        </a>
                    </div>
                @else
                    <form id="catalog-item-form" action="{{ route('admin.catalog-items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="redirect_to_type" value="{{ $returnToType ? 1 : 0 }}">

                        <section class="space-y-5">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Información básica</p>
                                <p class="text-sm text-gray-500 mt-1">Nombre, ubicación dentro del catálogo y precio principal.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('name') border-red-400 bg-red-50 @enderror"
                                       placeholder="Ej: Lavado premium, mantenimiento, asesoría">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Negocio *</label>
                                    <select name="catalog_type_id" id="catalog_type_id"
                                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                                        @unless($returnToType && $selectedTypeId > 0)
                                            <option value="">Selecciona un negocio</option>
                                        @endunless
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ (old('catalog_type_id', $selectedTypeId ?: null) == $type->id) ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('catalog_type_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                    <select name="catalog_category_id" id="catalog_category_id"
                                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('catalog_category_id') border-red-400 bg-red-50 @enderror">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-type="{{ $category->catalog_type_id }}" {{ (old('catalog_category_id', $selectedCategoryId ?: null) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->type->name }} / {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('catalog_category_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-2.5 text-gray-400 text-sm font-semibold">$</span>
                                    <input type="number" name="base_price" value="{{ old('base_price') }}"
                                           step="0.01" min="0"
                                           class="w-full border border-gray-200 rounded-lg pl-8 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('base_price') border-red-400 bg-red-50 @enderror"
                                           placeholder="0.00">
                                </div>
                                @error('base_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea name="description" rows="4"
                                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 resize-none @error('description') border-red-400 bg-red-50 @enderror"
                                          placeholder="Describe este servicio y para qué sirve dentro del negocio">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </section>

                        <section class="mt-8 border-t border-gray-100 pt-6 space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Cómo se usará</p>
                                <p class="text-sm text-gray-500 mt-1">Este negocio está configurado como servicio, por eso no maneja stock ni presentaciones.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 cursor-pointer">
                                    <input type="checkbox" name="active" value="1"
                                           {{ old('active', true) ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Activo</span>
                                        <span class="block text-xs text-gray-400">Visible en el catálogo público</span>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 cursor-pointer">
                                    <input type="checkbox" name="featured" value="1"
                                           {{ old('featured') ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Destacado</span>
                                        <span class="block text-xs text-gray-400">Aparece con más prioridad en la web</span>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 cursor-pointer">
                                    <input type="checkbox" name="reservable" value="1"
                                           {{ old('reservable') ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-700">Se puede reservar</span>
                                        <span class="block text-xs text-gray-400">Puede usarse para reservas</span>
                                    </span>
                                </label>

                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                    <span class="block text-sm font-semibold text-gray-700">Servicio sin inventario</span>
                                    <span class="block text-xs text-gray-400 mt-1">Los servicios no controlan stock ni aparecen como productos inventariables.</span>
                                </div>
                            </div>
                        </section>

                        <details class="mt-8 rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">Opciones avanzadas</summary>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                    <input type="text" name="slug" value="{{ old('slug') }}"
                                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white @error('slug') border-red-400 bg-red-50 @enderror"
                                           placeholder="Se genera automáticamente si queda vacío">
                                    @error('slug')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white @error('sort_order') border-red-400 bg-red-50 @enderror">
                                    @error('sort_order')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </details>

                        <div class="flex flex-wrap gap-3 pt-6 mt-8 border-t border-gray-100">
                            <button type="submit"
                                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                                Guardar Servicio
                            </button>
                            <a href="{{ ($returnToType && $selectedTypeId > 0) ? route('admin.catalog-types.show', $selectedTypeId) : ($selectedTypeId > 0 ? route('admin.catalog-items.index', ['catalog_type_id' => $selectedTypeId]) : route('admin.catalog.index')) }}"
                               class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                                Cancelar
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
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
