@extends('layouts.admin')

@section('title', 'Nuevo Producto')

@section('content')
    <div class="container mx-auto px-4 sm:px-6">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('admin.products.index') }}"
                class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                ←
            </a>

            <div>
                <div class="flex items-center gap-2">
                    <x-pixelicon-product-management class="w-8 h-8 text-gray-800" />
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Productos</h2>
                </div>
                <p class="text-gray-400 text-sm">Completa los campos para agregar un producto al catálogo</p>
            </div>
        </div>

        {{-- LAYOUT: En móvil apilado, en escritorio 2 columnas --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Columna izquierda (Imagen y estado) --}}
            <div class="w-full lg:w-1/3 space-y-6">

                {{-- Card imagen --}}
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen del Producto</p>

                    <div class="flex flex-col items-center mb-4">
                        <div id="img-preview"
                            class="w-28 h-28 sm:w-32 sm:h-32 bg-gray-100 rounded-xl flex items-center justify-center text-4xl mb-3 overflow-hidden border-2 border-dashed border-gray-200">
                            📦
                        </div>
                        <p class="text-xs text-gray-400 text-center" id="img-name">Sin imagen seleccionada</p>
                    </div>

                    <input type="file" name="image" id="image-input" accept="image/*" form="product-form"
                        class="hidden" onchange="previewImage(this)">

                    <button type="button" onclick="document.getElementById('image-input').click()"
                        class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        ☁️ Subir Imagen
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">JPG, PNG o WEBP — Máx. 2MB</p>

                    @error('image')
                        <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Card estado --}}
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Visibilidad</p>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="active" value="1" id="active-toggle" form="product-form"
                                {{ old('active', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-checked:bg-green-500 rounded-full transition-colors">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5">
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Producto activo</p>
                            <p class="text-xs text-gray-400">Visible en el catálogo público</p>
                        </div>
                    </label>
                </div>

            </div>

            {{-- Columna derecha (Formulario) --}}
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Información del Producto</p>

                    <form id="product-form" action="{{ route('admin.products.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- Nombre --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('name') border-red-400 bg-red-50 @enderror"
                                placeholder="Ej: Aceite de Motor 20W50">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría + Proveedor --}}
                        <div class="flex flex-col sm:flex-row gap-5 mb-5">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                <select name="category_id"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50">
                                    <option value="">Sin categoría</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                                <input type="text" name="provider" value="{{ old('provider') }}"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50"
                                    placeholder="Ej: Castrol, Mobil">
                                @error('provider')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" name="price" value="{{ old('price', 0) }}">

                        {{-- Variantes --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Presentaciones y Precios *</label>
                                <button type="button" id="add-variant-btn"
                                    class="text-xs font-semibold px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-gray-700 transition">
                                    + Agregar presentacion
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">Ejemplo: Litro, Galon, Caneca. El precio mostrado en catalogo sera el menor precio activo.</p>

                            <div id="variants-container" class="space-y-3"></div>
                            @error('variants')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea name="description" rows="4"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 resize-none"
                                placeholder="Descripción del producto...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                            <button type="submit"
                                class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                                Guardar Producto
                            </button>
                            <a href="{{ route('admin.products.index') }}"
                                class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                                Cancelar
                            </a>
                        </div>

                    </form>
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
            if (file.size > 2 * 1024 * 1024) {
                alert('La imagen no debe superar 2MB.');
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

        (function initVariants() {
            const container = document.getElementById('variants-container');
            const addBtn = document.getElementById('add-variant-btn');
            if (!container || !addBtn) return;

            const oldVariants = @json(old('variants', []));

            function renderRow(index, data = {}) {
                const row = document.createElement('div');
                row.className = 'grid grid-cols-1 md:grid-cols-12 gap-2 border border-gray-200 rounded-lg p-3 bg-gray-50';
                row.innerHTML = `
                    <input type="text" name="variants[${index}][name]" value="${data.name ?? ''}" placeholder="Nombre (Ej: Aceite 20W50)"
                        class="md:col-span-3 border border-gray-200 rounded px-3 py-2 text-sm" />
                    <input type="text" name="variants[${index}][presentation]" value="${data.presentation ?? ''}" placeholder="Presentacion (Litro, Galon)"
                        class="md:col-span-2 border border-gray-200 rounded px-3 py-2 text-sm" />
                    <input type="text" name="variants[${index}][specification]" value="${data.specification ?? ''}" placeholder="Especificacion (1L, 5L)"
                        class="md:col-span-2 border border-gray-200 rounded px-3 py-2 text-sm" />
                    <input type="number" name="variants[${index}][price]" value="${data.price ?? ''}" placeholder="Precio"
                        step="0.01" min="0" class="md:col-span-2 border border-gray-200 rounded px-3 py-2 text-sm" />
                    <input type="number" name="variants[${index}][stock]" value="${data.stock ?? ''}" placeholder="Stock"
                        min="0" class="md:col-span-2 border border-gray-200 rounded px-3 py-2 text-sm" />
                    <div class="md:col-span-1 flex items-center justify-between gap-2">
                        <label class="text-xs flex items-center gap-1">
                            <input type="checkbox" name="variants[${index}][active]" value="1" ${data.active === false ? '' : 'checked'}>
                            Activo
                        </label>
                        <button type="button" class="remove-variant text-red-600 text-xs font-semibold">Quitar</button>
                    </div>
                `;

                row.querySelector('.remove-variant').addEventListener('click', () => {
                    row.remove();
                    reindexRows();
                });

                return row;
            }

            function reindexRows() {
                Array.from(container.children).forEach((row, idx) => {
                    row.querySelectorAll('input').forEach((input) => {
                        if (!input.name) return;
                        input.name = input.name.replace(/variants\[\d+\]/, `variants[${idx}]`);
                    });
                });
            }

            function addRow(data = {}) {
                const index = container.children.length;
                container.appendChild(renderRow(index, data));
            }

            addBtn.addEventListener('click', () => addRow());

            if (oldVariants.length) {
                oldVariants.forEach((variant) => addRow(variant));
            } else {
                addRow({ name: '', presentation: 'Unidad', specification: '', price: '', stock: '', active: true });
            }
        })();
    </script>
@endpush
