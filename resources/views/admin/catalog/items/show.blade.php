@extends('layouts.admin')

@section('title', 'Detalle Item Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-items.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogItem->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle del item universal</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-1">
                    <img src="{{ $catalogItem->image_url }}" alt="{{ $catalogItem->name }}" class="w-full h-64 rounded-xl object-cover bg-gray-50 border border-gray-200">
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $catalogItem->name }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Tipo</p>
                            <p class="text-gray-700">{{ $catalogItem->type->name ?? 'Sin tipo' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Categoria</p>
                            <p class="text-gray-700">{{ $catalogItem->category->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                            <p class="text-gray-700 font-mono text-sm">{{ $catalogItem->slug ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Precio visible</p>
                            <p class="text-gray-700 font-semibold">${{ number_format($catalogItem->display_price, 2) }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Descripcion</p>
                        <p class="text-gray-700">{{ $catalogItem->description ?: 'Sin descripcion adicional.' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($catalogItem->active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                        @endif
                        @if($catalogItem->featured)
                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">Destacado</span>
                        @endif
                        @if($catalogItem->purchasable)
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Comprable</span>
                        @endif
                        @if($catalogItem->reservable)
                            <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-medium">Reservable</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Orden</p>
                    <p class="text-gray-700 font-semibold">{{ $catalogItem->sort_order }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Variantes</p>
                    <p class="text-gray-700 font-semibold">{{ $catalogItem->variants->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Origen legado</p>
                    <p class="text-gray-700">{{ $catalogItem->legacy_source_type && $catalogItem->legacy_source_id ? $catalogItem->legacy_source_type . ' #' . $catalogItem->legacy_source_id : 'Aun no migrado' }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.catalog-items.edit', $catalogItem) }}"
                   class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                    Editar
                </a>
                <form action="{{ route('admin.catalog-items.destroy', $catalogItem) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este item universal?')">
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
