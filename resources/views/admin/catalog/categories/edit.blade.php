@extends('layouts.admin')

@section('title', 'Editar Categoría Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-categories.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Editar Categoría Universal</h2>
            <p class="text-gray-400 text-sm">Modificando <strong class="text-gray-600">{{ $catalogCategory->name }}</strong></p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <form action="{{ route('admin.catalog-categories.update', $catalogCategory) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select name="catalog_type_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('catalog_type_id', $catalogCategory->catalog_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('catalog_type_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $catalogCategory->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $catalogCategory->slug) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('slug') border-red-400 bg-red-50 @enderror">
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="4"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('description') border-red-400 bg-red-50 @enderror">{{ old('description', $catalogCategory->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $catalogCategory->sort_order) }}" min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sort_order') border-red-400 bg-red-50 @enderror">
                        @error('sort_order')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-8">
                        <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                            <input type="checkbox" name="active" value="1" {{ old('active', $catalogCategory->active) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                            Categoría activa
                        </label>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Actualizar Categoría
                    </button>
                    <a href="{{ route('admin.catalog-categories.index') }}"
                       class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
