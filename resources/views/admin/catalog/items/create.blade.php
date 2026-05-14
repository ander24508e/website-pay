@extends('layouts.admin')

@section('title', 'Nuevo Item Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-items.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Nuevo Item Universal</h2>
            <p class="text-gray-400 text-sm">Crea un item flexible para cualquier tipo de negocio.</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-1/3 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen del Item</p>

                <div class="flex flex-col items-center mb-4">
                    <div id="img-preview"
                         class="w-28 h-28 sm:w-32 sm:h-32 bg-gray-100 rounded-xl flex items-center justify-center text-4xl mb-3 overflow-hidden border-2 border-dashed border-gray-200">
                        🧩
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

            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Comportamiento</p>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="active" value="1" form="catalog-item-form"
                           {{ old('active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Item activo</p>
                        <p class="text-xs text-gray-400">Visible en el catalogo futuro</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="featured" value="1" form="catalog-item-form"
                           {{ old('featured') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Destacado</p>
                        <p class="text-xs text-gray-400">Prioridad visual para website</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="purchasable" value="1" form="catalog-item-form"
                           {{ old('purchasable', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Comprable</p>
                        <p class="text-xs text-gray-400">Puede ir al carrito o compra futura</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="reservable" value="1" form="catalog-item-form"
                           {{ old('reservable') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Reservable</p>
                        <p class="text-xs text-gray-400">Puede usarse para reservas futuras</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                @if($types->isEmpty())
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                        Primero necesitas crear al menos un tipo de catalogo antes de registrar items universales.
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('admin.catalog-types.create') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                            Crear Tipo de Catalogo
                        </a>
                    </div>
                @else
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Informacion del Item</p>

                    <form id="catalog-item-form" action="{{ route('admin.catalog-items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('name') border-red-400 bg-red-50 @enderror"
                                   placeholder="Ej: Ceviche, Lavado premium, Coctel de la casa">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                                <select name="catalog_type_id" id="catalog_type_id"
                                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                                    <option value="">Selecciona un tipo</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ old('catalog_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('catalog_type_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                <select name="catalog_category_id" id="catalog_category_id"
                                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('catalog_category_id') border-red-400 bg-red-50 @enderror">
                                    <option value="">Sin categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-type="{{ $category->catalog_type_id }}" {{ old('catalog_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->type->name }} / {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('catalog_category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" name="slug" value="{{ old('slug') }}"
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('slug') border-red-400 bg-red-50 @enderror"
                                       placeholder="Se genera automaticamente si lo dejas vacio">
                                @error('slug')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio base</label>
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
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                            <textarea name="description" rows="4"
                                      class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 resize-none @error('description') border-red-400 bg-red-50 @enderror"
                                      placeholder="Describe este item y para que sirve dentro del negocio">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('sort_order') border-red-400 bg-red-50 @enderror">
                            @error('sort_order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                            <button type="submit"
                                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                                Guardar Item
                            </button>
                            <a href="{{ route('admin.catalog-items.index') }}"
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
})();
</script>
@endpush
