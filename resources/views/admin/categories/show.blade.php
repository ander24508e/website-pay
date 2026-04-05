@extends('layouts.admin')

@section('title', 'Detalle Categoría')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            ←
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">🏷️ {{ $category->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle de la categoría</p>
        </div>
    </div>

    {{-- TARJETA DE DETALLE --}}
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                    <p class="font-semibold text-gray-800 break-words">{{ $category->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Tipo</p>
                    <p class="text-gray-700">
                        {{ $category->type === 'product' ? '📦 Producto' : '🛠️ Servicio' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">
                        {{ $category->type === 'product' ? 'Productos' : 'Servicios' }} asignados
                    </p>
                    <p class="text-gray-700 text-lg font-semibold">
                        {{ $category->type === 'product' ? $category->products->count() : $category->services->count() }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    ✏️ Editar
                </a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta categoría?')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                        🗑 Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection