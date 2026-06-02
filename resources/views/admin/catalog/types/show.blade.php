@extends('layouts.admin')

@section('title', 'Detalle de Sección')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-4">
        <a href="{{ route('admin.catalog.index') }}" class="hover:text-gray-600 transition">Catalogo</a>
        <span>/</span>
        <span>Secciones</span>
        <span>/</span>
        <span class="text-gray-600 font-semibold">{{ $catalogType->name }}</span>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.catalog.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogType->name }}</h2>
            <p class="text-gray-400 text-sm">Administra esta sección y lo que se mostrará en tu página web.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Sección</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogType->name }}</p>
            <p class="text-xs text-gray-400 mt-1">Área principal del catálogo público</p>
        </div>
        <a href="{{ route('admin.catalog-categories.index', ['catalog_type_id' => $catalogType->id]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Categorias</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogType->categories_count }}</p>
            <p class="text-xs text-gray-400 mt-1">Ver categorías de esta sección</p>
        </a>
        <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogType->id]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Productos/Servicios</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogType->items_count }}</p>
            <p class="text-xs text-gray-400 mt-1">Ver productos y servicios de esta sección</p>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
                <div class="flex flex-wrap gap-3 mb-6">
                    <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                       class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        + Nuevo Producto o Servicio
                    </a>
                    <a href="{{ route('admin.catalog-categories.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                       class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        + Categoria
                    </a>
                </div>

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
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Productos/Servicios</p>
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
                          onsubmit="return confirm('¿Eliminar esta sección?')">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Categorías de esta sección</h3>
                        <p class="text-sm text-gray-400">Te ayudan a organizar productos y servicios.</p>
                    </div>
                    <a href="{{ route('admin.catalog-categories.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                       class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        + Nueva Categoria
                    </a>
                </div>

                @if($catalogType->categories->isEmpty())
                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-sm text-gray-500">
                        Esta sección aún no tiene categorías. Puedes crear productos o servicios sin categoría, o agregar una para ordenar mejor.
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($catalogType->categories as $category)
                            <a href="{{ route('admin.catalog-categories.show', $category) }}" class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium hover:bg-gray-200 transition">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Productos y servicios dentro de {{ $catalogType->name }}</h3>
                        <p class="text-sm text-gray-400">Estos son los elementos que podrán mostrarse en la web o usarse en ventas.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                           class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                            + Agregar
                        </a>
                        <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogType->id]) }}"
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                            Mostrar Todo
                        </a>
                    </div>
                </div>

                @if($catalogType->items->isEmpty())
                    <div class="p-6 sm:p-8 text-center text-gray-500">
                        <p class="font-medium text-gray-700 mb-2">Todavía no hay productos o servicios en esta sección.</p>
                        <p class="text-sm mb-5">Empieza creando el primero para que luego se vea en la web o pueda venderse.</p>
                        <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                            Crear Primer Producto o Servicio
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-[720px] w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3">Producto/Servicio</th>
                                    <th class="px-4 sm:px-6 py-3">Categoria</th>
                                    <th class="px-4 sm:px-6 py-3">Precio</th>
                                    <th class="px-4 sm:px-6 py-3">Presentaciones</th>
                                    <th class="px-4 sm:px-6 py-3">Estado</th>
                                    <th class="px-4 sm:px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($catalogType->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 sm:px-6 py-4">
                                            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($item->description, 80) ?: 'Sin descripcion adicional' }}</p>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 text-gray-600">
                                            {{ $item->category?->name ?: 'Sin categoria' }}
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
                                                <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $item->id, 'catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}" class="text-gray-700 hover:text-gray-900 text-sm font-medium">+ Presentacion</a>
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
