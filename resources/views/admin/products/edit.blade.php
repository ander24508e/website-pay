@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.products.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            ←
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">✏️ Editar Producto</h2>
            <p class="text-gray-400 text-sm">Modificando <strong class="text-gray-600">{{ $product->name }}</strong></p>
        </div>
    </div>

    {{-- LAYOUT --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Columna izquierda --}}
        <div class="w-full lg:w-1/3 space-y-6">

            {{-- Card imagen --}}
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen del Producto</p>

                <div class="flex flex-col items-center mb-4">
                    <div id="img-preview"
                         class="w-28 h-28 sm:w-32 sm:h-32 bg-gray-100 rounded-xl flex items-center justify-center text-4xl mb-3 overflow-hidden border-2 border-dashed border-gray-200">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                        @else
                            📦
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 text-center" id="img-name">
                        {{ $product->image ? basename($product->image) : 'Sin imagen' }}
                    </p>
                </div>

                <input type="file" name="image" id="image-input" accept="image/*"
                       form="product-form" class="hidden" onchange="previewImage(this)">

                <button type="button" onclick="document.getElementById('image-input').click()"
                        class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    ☁️ {{ $product->image ? 'Cambiar Imagen' : 'Subir Imagen' }}
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
                        <input type="checkbox" name="active" value="1" form="product-form"
                               {{ old('active', $product->active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-checked:bg-green-500 rounded-full transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Producto activo</p>
                        <p class="text-xs text-gray-400">Visible en el catálogo público</p>
                    </div>
                </label>
            </div>

            {{-- Zona de peligro --}}
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-red-100">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-3">Zona de peligro</p>
                <p class="text-xs text-gray-400 mb-4">Esta acción es permanente y no se puede deshacer.</p>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                      onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full bg-red-50 text-red-600 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                        🗑 Eliminar Producto
                    </button>
                </form>
            </div>

        </div>

        {{-- Columna derecha (Formulario) --}}
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Información del Producto</p>

                <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('name') border-red-400 bg-red-50 @enderror">
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
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                            <input type="text" name="provider" value="{{ old('provider', $product->provider) }}"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50"
                                   placeholder="Ej: Castrol, Mobil">
                            @error('provider')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Precio --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-400 text-sm font-semibold">$</span>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}"
                                   step="0.01" min="0"
                                   class="w-full border border-gray-200 rounded-lg pl-8 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 @error('price') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="4"
                                  class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 bg-gray-50 resize-none">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                        <button type="submit"
                                class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                            Actualizar Producto
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
</script>
@endpush