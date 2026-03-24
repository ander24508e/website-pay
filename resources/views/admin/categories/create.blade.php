@extends('layouts.admin')

@section('title', 'Nueva Categoría')

@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-800">← Volver</a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Nueva Categoría</h2>
        <p class="text-gray-500 text-sm mt-1">Crea una categoría para productos o servicios</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-8 max-w-lg">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 @enderror"
                   placeholder="Ej: Lavado, Aceites de Motor">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
            <select name="type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400">
                <option value="">Selecciona un tipo</option>
                <option value="service"  {{ old('type') == 'service'  ? 'selected' : '' }}>🛠️ Servicio</option>
                <option value="product"  {{ old('type') == 'product'  ? 'selected' : '' }}>📦 Producto</option>
            </select>
            @error('type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
                Guardar Categoría
            </button>
            <a href="{{ route('admin.categories.index') }}"
               class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection