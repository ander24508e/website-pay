@extends('layouts.admin')

@section('title', 'Nueva Sección')

@section('content')
    @php
        $selectedBusinessModel = old('business_model', request('business_model', 'services'));
    @endphp

    <div class="container mx-auto px-4 sm:px-6">
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('admin.catalog.index') }}"
                class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                <span aria-hidden="true">&larr;</span>
            </a>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Crear Negocio</h2>
            </div>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
                <form action="{{ route('admin.catalog-types.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror"
                            placeholder="Ej: Servicios, Productos, Comida, Bebidas">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Modelo del negocio *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="flex gap-3 rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-gray-400 transition">
                                <input type="radio" name="business_model" value="services"
                                    class="mt-1 text-gray-900 focus:ring-gray-400"
                                    {{ $selectedBusinessModel === 'services' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-semibold text-gray-800">Servicios</span>
                                    <span class="block text-xs text-gray-500 mt-1">Para reservas, trabajos o atención sin
                                        stock directo.</span>
                                </span>
                            </label>
                            <label
                                class="flex gap-3 rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-gray-400 transition">
                                <input type="radio" name="business_model" value="products"
                                    class="mt-1 text-gray-900 focus:ring-gray-400"
                                    {{ $selectedBusinessModel === 'products' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-semibold text-gray-800">Productos</span>
                                    <span class="block text-xs text-gray-500 mt-1">Para artículos físicos que podrán manejar
                                        stock e inventario.</span>
                                </span>
                            </label>
                        </div>
                        @error('business_model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('slug') border-red-400 bg-red-50 @enderror"
                                placeholder="Se genera automáticamente si lo dejas vacío">
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('description') border-red-400 bg-red-50 @enderror"
                            placeholder="Explica que tipo de items agrupará esta sección">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('sort_order') border-red-400 bg-red-50 @enderror">
                            @error('sort_order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center pt-8">
                            <label class="inline-flex items-center gap-3 text-sm text-gray-700">
                                <input type="checkbox" name="active" value="1"
                                    {{ old('active', '1') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                Tipo activo
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                            Guardar Sección
                        </button>
                        <a href="{{ route('admin.catalog.index') }}"
                            class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


