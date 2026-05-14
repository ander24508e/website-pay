@extends('layouts.admin')

@section('title', 'Nueva Variante Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-variants.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Nueva Variante Universal</h2>
            <p class="text-gray-400 text-sm">Agrega una presentacion o version a un item del nuevo catalogo.</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            @if($items->isEmpty())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    Primero necesitas crear al menos un item universal para poder registrar variantes.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.catalog-items.create') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                        Crear Item Universal
                    </a>
                </div>
            @else
                <form action="{{ route('admin.catalog-variants.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item *</label>
                        <select name="catalog_item_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('catalog_item_id') border-red-400 bg-red-50 @enderror">
                            <option value="">Selecciona un item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('catalog_item_id', $selectedItemId) == $item->id ? 'selected' : '' }}>
                                    {{ $item->type->name ?? 'Tipo' }} / {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('catalog_item_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror"
                               placeholder="Ej: Grande, Combo 2, 1 Litro">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Presentacion</label>
                            <input type="text" name="presentation" value="{{ old('presentation') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('presentation') border-red-400 bg-red-50 @enderror"
                                   placeholder="Ej: Botella, Plato, Galon">
                            @error('presentation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especificacion</label>
                            <input type="text" name="specification" value="{{ old('specification') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('specification') border-red-400 bg-red-50 @enderror"
                                   placeholder="Ej: 500ml, 2 personas">
                            @error('specification')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sku') border-red-400 bg-red-50 @enderror"
                                   placeholder="Codigo interno opcional">
                            @error('sku')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                            <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('price') border-red-400 bg-red-50 @enderror"
                                   placeholder="0.00">
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock') }}" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('stock') border-red-400 bg-red-50 @enderror"
                                   placeholder="Opcional">
                            @error('stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sort_order') border-red-400 bg-red-50 @enderror">
                            @error('sort_order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                            <input type="checkbox" name="active" value="1" {{ old('active', '1') ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                            Variante activa
                        </label>
                        <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                            Variante base
                        </label>
                    </div>

                    <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-500 mb-8">
                        La variante base es la que se selecciona primero en el website cuando el usuario abre el detalle del item.
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit"
                                class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                            Guardar Variante
                        </button>
                        <a href="{{ route('admin.catalog-variants.index') }}"
                           class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                            Cancelar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
