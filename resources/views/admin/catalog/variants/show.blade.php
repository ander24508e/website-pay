@extends('layouts.admin')

@section('title', 'Detalle Variante Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-variants.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogVariant->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle de la variante universal</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                    <p class="font-semibold text-gray-800 break-words">{{ $catalogVariant->name }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Item</p>
                        <p class="text-gray-700">{{ $catalogVariant->item->name ?? 'Sin item' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Tipo</p>
                        <p class="text-gray-700">{{ $catalogVariant->item->type->name ?? 'Sin tipo' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Presentacion</p>
                        <p class="text-gray-700">{{ $catalogVariant->presentation ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Especificacion</p>
                        <p class="text-gray-700">{{ $catalogVariant->specification ?: '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">SKU</p>
                        <p class="text-gray-700 font-mono text-sm">{{ $catalogVariant->sku ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Precio</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogVariant->price !== null ? '$' . number_format((float) $catalogVariant->price, 2) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Stock</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogVariant->stock ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Orden</p>
                        <p class="text-gray-700 font-semibold">{{ $catalogVariant->sort_order }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                        @if($catalogVariant->active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Activa</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">Oculta</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Base</p>
                        @if($catalogVariant->is_default)
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Si</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">No</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.catalog-variants.edit', $catalogVariant) }}"
                   class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    Editar
                </a>
                <form action="{{ route('admin.catalog-variants.destroy', $catalogVariant) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta variante universal?')">
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
