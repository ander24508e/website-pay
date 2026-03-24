@extends('layouts.admin')

@section('title', 'Detalle Categoría')

@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-800">← Volver</a>
    <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
</div>

<div class="bg-white rounded-xl shadow-sm p-8 max-w-lg">
    <div class="space-y-4 mb-8">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
            <p class="font-semibold text-gray-800">{{ $category->name }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Tipo</p>
            <p class="text-gray-700">{{ $category->type === 'product' ? '📦 Producto' : '🛠️ Servicio' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">
                {{ $category->type === 'product' ? 'Productos' : 'Servicios' }} asignados
            </p>
            <p class="text-gray-700">
                {{ $category->type === 'product' ? $category->products->count() : $category->services->count() }}
            </p>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.categories.edit', $category) }}"
           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
            Editar
        </a>
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
              onsubmit="return confirm('¿Eliminar esta categoría?')">
            @csrf @method('DELETE')
            <button class="bg-red-100 text-red-700 px-5 py-2.5 rounded-lg hover:bg-red-200 transition font-medium">
                Eliminar
            </button>
        </form>
    </div>
</div>

@endsection