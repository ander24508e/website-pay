<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1">Producto *</label>
    <select name="catalog_item_id"
        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('catalog_item_id') border-red-400 bg-red-50 @enderror">
        @if ($showEmptyItemOption)
            <option value="">Selecciona un producto</option>
        @endif
        @foreach($items as $item)
            <option value="{{ $item->id }}" {{ old('catalog_item_id', $selectedItemId ?? $catalogVariant?->catalog_item_id) == $item->id ? 'selected' : '' }}>
                {{ isset($item->type) && isset($item->type->name) ? $item->type->name : 'Seccion' }} / {{ $item->name }}
            </option>
        @endforeach
    </select>
    @error('catalog_item_id')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
    <input type="text" name="name" value="{{ old('name', $catalogVariant?->name) }}"
        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror"
        placeholder="Ej: Grande, 1 Litro, Galon">
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Presentacion</label>
        <input type="text" name="presentation" value="{{ old('presentation', $catalogVariant?->presentation) }}"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('presentation') border-red-400 bg-red-50 @enderror"
            placeholder="Ej: Botella, Galon">
        @error('presentation')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Especificacion</label>
        <input type="text" name="specification" value="{{ old('specification', $catalogVariant?->specification) }}"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('specification') border-red-400 bg-red-50 @enderror"
            placeholder="Ej: 500ml, 2 unidades">
        @error('specification')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $catalogVariant?->sku) }}"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sku') border-red-400 bg-red-50 @enderror"
            placeholder="Codigo interno opcional">
        @error('sku')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
        <input type="number" name="price" value="{{ old('price', $catalogVariant?->price) }}" step="0.01" min="0"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('price') border-red-400 bg-red-50 @enderror"
            placeholder="0.00">
        @error('price')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
        <input type="number" name="cost_price" value="{{ old('cost_price', $catalogVariant?->cost_price) }}" step="0.01" min="0"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('cost_price') border-red-400 bg-red-50 @enderror"
            placeholder="0.00">
        @error('cost_price')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $catalogVariant?->stock) }}" min="0"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('stock') border-red-400 bg-red-50 @enderror"
            placeholder="Opcional">
        @error('stock')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Stock minimo</label>
        <input type="number" name="min_stock" value="{{ old('min_stock', $catalogVariant?->min_stock ?? 0) }}" min="0"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('min_stock') border-red-400 bg-red-50 @enderror"
            placeholder="0">
        @error('min_stock')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    <label class="inline-flex items-center gap-3 text-sm text-gray-700">
        <input type="checkbox" name="active" value="1" {{ old('active', $catalogVariant?->active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
        Presentacion activa
    </label>
    <label class="inline-flex items-center gap-3 text-sm text-gray-700">
        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $catalogVariant?->is_default ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
        Presentacion principal
    </label>
</div>

<div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-500 mb-8">
    La presentacion principal es la que se selecciona primero en la web y reemplaza a cualquier otra principal del mismo producto.
</div>
