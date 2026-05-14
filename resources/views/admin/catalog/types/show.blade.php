@extends('layouts.admin')

@section('title', 'Detalle Tipo de Catalogo')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-types.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogType->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle del tipo de catalogo</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                    <p class="font-semibold text-gray-800 break-words">{{ $catalogType->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                    <p class="text-gray-700 font-mono text-sm">{{ $catalogType->slug ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Icono</p>
                    <p class="text-gray-700">{{ $catalogType->icon ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Descripcion</p>
                    <p class="text-gray-700">{{ $catalogType->description ?: 'Sin descripcion adicional.' }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Orden</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogType->sort_order }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Categorias</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogType->categories_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Items</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogType->items_count }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                    @if($catalogType->active)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Activo</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">Oculto</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.catalog-types.edit', $catalogType) }}"
                   class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    Editar
                </a>
                <form action="{{ route('admin.catalog-types.destroy', $catalogType) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este tipo de catalogo?')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
