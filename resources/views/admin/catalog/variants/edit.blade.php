@extends('layouts.admin')

@section('title', 'Editar Variante Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-variants.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Editar Variante Universal</h2>
            <p class="text-gray-400 text-sm">Modificando <strong class="text-gray-600">{{ $catalogVariant->name }}</strong></p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <form action="{{ route('admin.catalog-variants.update', $catalogVariant) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item *</label>
                    <select name="catalog_item_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('catalog_item_id') border-red-400 bg-red-50 @enderror">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('catalog_item_id', $catalogVariant->catalog_item_id) == $item->id ? 'selected' : '' }}>
                                {{ isset($item->type) && isset($item->type->name) ? $item->type->name : 'Tipo' }} / {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('catalog_item_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $catalogVariant->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PresentaciÃ³n</label>
                        <input type="text" name="presentation" value="{{ old('presentation', $catalogVariant->presentation) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('presentation') border-red-400 bg-red-50 @enderror">
                        @error('presentation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especificacion</label>
                        <input type="text" name="specification" value="{{ old('specification', $catalogVariant->specification) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('specification') border-red-400 bg-red-50 @enderror">
                        @error('specification')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $catalogVariant->sku) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sku') border-red-400 bg-red-50 @enderror">
                        @error('sku')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <input type="number" name="price" value="{{ old('price', $catalogVariant->price) }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('price') border-red-400 bg-red-50 @enderror">
                        @error('price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $catalogVariant->cost_price) }}" step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('cost_price') border-red-400 bg-red-50 @enderror">
                        @error('cost_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock" value="{{ old('stock', $catalogVariant->stock) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('stock') border-red-400 bg-red-50 @enderror">
                        @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock mínimo</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $catalogVariant->min_stock) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('min_stock') border-red-400 bg-red-50 @enderror">
                        @error('min_stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $catalogVariant->sort_order) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sort_order') border-red-400 bg-red-50 @enderror">
                        @error('sort_order')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="active" value="1" {{ old('active', $catalogVariant->active) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                        Variante activa
                    </label>
                    <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $catalogVariant->is_default) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                        Variante base
                    </label>
                </div>

                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-500 mb-8">
                    Si marcas esta variante como base, reemplazara a cualquier otra variante base del mismo item.
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Actualizar Variante
                    </button>
                    <a href="{{ route('admin.catalog-variants.index') }}"
                       class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
