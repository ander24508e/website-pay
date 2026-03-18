@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-800 transition">
        ← Volver
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Editar Producto</h2>
        <p class="text-gray-500 text-sm mt-1">Modifica los datos de <strong>{{ $product->name }}</strong></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 @enderror">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Categoría --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="category_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">Sin categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Proveedor --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
            <input type="text" name="provider" value="{{ old('provider', $product->provider) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400">
        </div>

        {{-- Descripción --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="description" rows="4"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- Precio --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
            <div class="relative">
                <span class="absolute left-4 top-2.5 text-gray-500">$</span>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                       class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('price') border-red-400 @enderror">
            </div>
            @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Imagen actual --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>
            @if($product->image)
                <div class="mb-3">
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-32 h-32 rounded-lg object-cover border border-gray-200">
                    <p class="text-gray-400 text-xs mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400">
        </div>

        {{-- Activo --}}
        <div class="mb-8">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="active" value="1"
                       {{ old('active', $product->active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded">
                <span class="text-sm font-medium text-gray-700">Producto activo (visible en catálogo)</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
                Actualizar Producto
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection