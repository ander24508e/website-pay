@extends('layouts.admin')

@section('title', 'Detalle CategorÃ­a Universal')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-4">
        <a href="{{ route('admin.catalog.index') }}" class="hover:text-gray-600 transition">CatÃ¡logo</a>
        <span>/</span>
        <a href="{{ route('admin.catalog-types.show', $catalogCategory->catalog_type_id) }}" class="hover:text-gray-600 transition">{{ isset($catalogCategory->type) && isset($catalogCategory->type->name) ? $catalogCategory->type->name : 'Subnegocio' }}</a>
        <span>/</span>
        <span class="text-gray-600 font-semibold">{{ $catalogCategory->name }}</span>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog-categories.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogCategory->name }}</h2>
            <p class="text-gray-400 text-sm">Administra esta categorÃ­a y sus items dentro del catÃ¡logo.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('admin.catalog-types.show', $catalogCategory->catalog_type_id) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">CatÃ¡logo Padre</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ isset($catalogCategory->type) && isset($catalogCategory->type->name) ? $catalogCategory->type->name : 'Sin tipo' }}</p>
            <p class="text-xs text-gray-400 mt-1">Volver al subnegocio</p>
        </a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">CategorÃ­a</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogCategory->name }}</p>
            <p class="text-xs text-gray-400 mt-1">SecciÃ³n interna del catÃ¡logo</p>
        </div>
        <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogCategory->catalog_type_id, 'catalog_category_id' => $catalogCategory->id]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Items</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogCategory->items_count }}</p>
            <p class="text-xs text-gray-400 mt-1">Ver items de esta categorÃ­a</p>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
                <div class="flex flex-wrap gap-3 mb-6">
                    <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogCategory->catalog_type_id, 'catalog_category_id' => $catalogCategory->id, 'return_to_type' => 1]) }}"
                       class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        + Nuevo Item
                    </a>
                    <a href="{{ route('admin.catalog-types.show', $catalogCategory->catalog_type_id) }}"
                       class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Ver CatÃ¡logo Padre
                    </a>
                    <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogCategory->catalog_type_id, 'catalog_category_id' => $catalogCategory->id]) }}"
                       class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Ver Items
                    </a>
                </div>

                <div class="space-y-4 mb-8">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $catalogCategory->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">CatÃ¡logo</p>
                        <p class="text-gray-700">{{ isset($catalogCategory->type) && isset($catalogCategory->type->name) ? $catalogCategory->type->name : 'Sin tipo' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                        <p class="text-gray-700 font-mono text-sm">{{ $catalogCategory->slug ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">DescripciÃ³n</p>
                        <p class="text-gray-700">{{ $catalogCategory->description ?: 'Sin descripciÃ³n adicional.' }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Orden</p>
                            <p class="text-gray-700 font-semibold">{{ $catalogCategory->sort_order }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Items</p>
                            <p class="text-gray-700 font-semibold">{{ $catalogCategory->items_count }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                            @if($catalogCategory->active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Activa</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">Oculta</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.catalog-categories.edit', $catalogCategory) }}"
                       class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Editar
                    </a>
                    <form action="{{ route('admin.catalog-categories.destroy', $catalogCategory) }}" method="POST"
                          onsubmit="return confirm('Â¿Eliminar esta categorÃ­a universal?')">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Items dentro de {{ $catalogCategory->name }}</h3>
                        <p class="text-sm text-gray-400">Estos items quedan agrupados dentro de esta categorÃ­a en el catÃ¡logo.</p>
                    </div>
                    <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogCategory->catalog_type_id, 'catalog_category_id' => $catalogCategory->id, 'return_to_type' => 1]) }}"
                       class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                        + Agregar Item
                    </a>
                </div>

                @if($catalogCategory->items->isEmpty())
                    <div class="p-6 sm:p-8 text-center text-gray-500">
                        <p class="font-medium text-gray-700 mb-2">TodavÃ­a no hay items en esta categorÃ­a.</p>
                        <p class="text-sm mb-5">Crea el primero y quedarÃ¡ organizado automÃ¡ticamente dentro de esta secciÃ³n.</p>
                        <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogCategory->catalog_type_id, 'catalog_category_id' => $catalogCategory->id, 'return_to_type' => 1]) }}"
                           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                            Crear Primer Item
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-[720px] w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3">Item</th>
                                    <th class="px-4 sm:px-6 py-3">Precio</th>
                                    <th class="px-4 sm:px-6 py-3">Variantes</th>
                                    <th class="px-4 sm:px-6 py-3">Estado</th>
                                    <th class="px-4 sm:px-6 py-3">Acci?nes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($catalogCategory->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 sm:px-6 py-4">
                                            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($item->description, 80) ?: 'Sin descripciÃ³n adicional' }}</p>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 font-semibold text-gray-800">
                                            {{ $item->base_price !== null ? '$' . number_format((float) $item->base_price, 2) : '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-gray-700">
                                            {{ $item->variants_count }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            @if($item->active)
                                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('admin.catalog-items.show', $item) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver</a>
                                                <a href="{{ route('admin.catalog-items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Editar</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

