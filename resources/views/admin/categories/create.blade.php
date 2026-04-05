@extends('layouts.admin')

@section('title', 'Nueva Categoría')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            ←
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">🏷️ Nueva Categoría</h2>
            <p class="text-gray-400 text-sm">Crea una categoría para productos o servicios</p>
        </div>
    </div>

    {{-- FORMULARIO (Centrado y responsive) --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('name') border-red-400 bg-red-50 @enderror"
                           placeholder="Ej: Lavado, Aceites de Motor">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select name="type"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 @error('type') border-red-400 bg-red-50 @enderror">
                        <option value="">Selecciona un tipo</option>
                        <option value="service"  {{ old('type') == 'service'  ? 'selected' : '' }}>🛠️ Servicio</option>
                        <option value="product"  {{ old('type') == 'product'  ? 'selected' : '' }}>📦 Producto</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Guardar Categoría
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                       class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection