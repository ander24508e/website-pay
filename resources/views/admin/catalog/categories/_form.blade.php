<div class="grid gap-4 p-4 xl:overflow-y-auto">
    <div class="min-w-0 space-y-4">
        <section class="space-y-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Informacion principal</p>
                <p class="text-xs text-gray-500">Nombre, slug y descripcion de la categoria.</p>
            </div>

            @if ($showTypeSelector)
                <div>
                    <label class="{{ $labelClass }}">Seccion *</label>
                    <select name="catalog_type_id"
                        class="{{ $inputClass }} @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                        <option value="">Selecciona una seccion</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" {{ old('catalog_type_id', $selectedTypeId ?? $catalogCategory?->catalog_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('catalog_type_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <input type="hidden" name="catalog_type_id" value="{{ $selectedTypeId ?? $catalogCategory->catalog_type_id }}">
            @endif

            <div>
                <label class="{{ $labelClass }}">Nombre *</label>
                <input type="text" name="name" id="category_name" value="{{ old('name', $catalogCategory?->name) }}"
                    class="{{ $inputClass }} @error('name') border-red-400 bg-red-50 @enderror"
                    placeholder="Categoria de tu negocio">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">Slug</label>
                <input type="text" name="slug" id="category_slug" value="{{ old('slug', $catalogCategory?->slug) }}"
                    class="{{ $inputClass }} bg-white text-gray-500 @error('slug') border-red-400 bg-red-50 @enderror"
                    placeholder="Se genera automaticamente" readonly>
                @error('slug')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">Descripcion</label>
                <textarea name="description" rows="5"
                    class="{{ $inputClass }} resize-none @error('description') border-red-400 bg-red-50 @enderror"
                    placeholder="Describe que agrupa esta categoria">{{ old('description', $catalogCategory?->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </section>
    </div>
</div>
