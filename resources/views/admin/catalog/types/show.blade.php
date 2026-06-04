@extends('layouts.admin')

@section('title', 'Detalle de Sección')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 min-h-0 xl:h-[calc(100vh-3rem)] xl:overflow-hidden xl:flex xl:flex-col">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4 shrink-0">
            <a href="{{ route('admin.catalog.index') }}"
                class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800 shrink-0">
                <span aria-hidden="true">&larr;</span>
            </a>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogType->name }}</h2>
                <p class="text-gray-400 text-sm">Administra esta sección y lo que se mostrará en tu página web.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:min-h-0 xl:flex-1">
            <aside class="xl:col-span-4 xl:min-h-0">
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 xl:h-full xl:max-h-full xl:overflow-y-auto">
                    <div class="flex flex-wrap gap-3 mb-5">
                        <h3 class="text-lg font-bold text-gray-800">Informacion del Negocio</h3>
                    </div>

                    <div class="space-y-4 mb-6 min-w-0">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                            <p class="font-semibold text-gray-800 break-words">{{ $catalogType->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                            <p class="text-gray-700 font-mono text-sm break-all">{{ $catalogType->slug ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Icono</p>
                            <p class="text-gray-700">{{ $catalogType->icon ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Descripcion</p>
                            <p class="text-gray-700 break-words">{{ $catalogType->description ?: 'Sin descripcion adicional.' }}</p>
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
                                <p class="text-xs text-gray-400 uppercase tracking-wide">Productos</p>
                                <p class="text-gray-700 font-semibold">{{ $catalogType->items_count }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                            @if ($catalogType->active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Activo</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">Oculto</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.catalog-types.edit', $catalogType) }}"
                            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                            Editar
                        </a>
                        <form action="{{ route('admin.catalog-types.destroy', $catalogType) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta sección?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <section class="xl:col-span-8 xl:min-h-0 flex flex-col xl:grid xl:grid-rows-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden xl:min-h-0 flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800">Categorías de esta sección</h3>
                            <p class="text-sm text-gray-400">Te ayudan a organizar productos y servicios.</p>
                        </div>
                        <a href="{{ route('admin.catalog-categories.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium text-center shrink-0">
                            + Nueva Categoria
                        </a>
                    </div>

                    @if ($catalogType->categories->isEmpty())
                        <div class="m-4 sm:m-5 rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-sm text-gray-500">
                            Esta sección aún no tiene categorías. Puedes crear productos o servicios sin categoría, o agregar una para ordenar mejor.
                        </div>
                    @else
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach ($catalogType->categories as $category)
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 break-words">{{ $category->name }}</p>
                                            <p class="text-xs text-gray-400 mt-1 break-words">{{ $category->slug ?: 'Sin slug' }}</p>
                                        </div>
                                        @if ($category->active)
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Activo</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Oculto</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3 break-words">{{ $category->description ?: 'Sin descripcion adicional' }}</p>
                                    <div class="grid grid-cols-2 gap-3 text-sm mt-3">
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Productos</p>
                                            <p class="font-semibold text-gray-700">{{ $category->items_count }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Orden</p>
                                            <p class="font-semibold text-gray-700">{{ $category->sort_order }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-3">
                                        <a href="{{ route('admin.catalog-categories.show', $category) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                            title="Ver categoria" aria-label="Ver categoria">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-categories.edit', $category) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                            title="Editar categoria" aria-label="Editar categoria">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-y-auto overflow-x-hidden max-h-[320px] xl:max-h-none xl:min-h-0 flex-1">
                            <table class="w-full table-fixed text-sm text-left">
                                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                    <tr>
                                    <th class="w-[28%] px-3 py-2.5 text-gray-600 font-semibold text-center">Categoria</th>
                                    <th class="w-[18%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden sm:table-cell">Slug</th>
                                        <th class="w-[12%] px-3 py-2.5 text-gray-600 font-semibold text-center">Prod.</th>
                                        <th class="w-[10%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden md:table-cell">Orden</th>
                                    <th class="w-[14%] px-3 py-2.5 text-gray-600 font-semibold text-center">Estado</th>
                                        <th class="w-[18%] px-3 py-2.5 text-gray-600 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($catalogType->categories as $category)
                                        <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 align-top text-center">
                                                <p class="font-semibold text-gray-800 truncate">{{ $category->name }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5 leading-tight line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($category->description, 55) ?: 'Sin descripcion adicional' }}
                                                </p>
                                            </td>
                                    <td class="px-3 py-2.5 text-gray-600 text-center truncate hidden sm:table-cell">{{ $category->slug ?: '-' }}</td>
                                            <td class="px-3 py-2.5 text-gray-700 text-center">{{ $category->items_count }}</td>
                                            <td class="px-3 py-2.5 text-gray-700 text-center hidden md:table-cell">{{ $category->sort_order }}</td>
                                    <td class="px-3 py-2.5 text-center">
                                                @if ($category->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium">Activo</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium">Oculto</span>
                                                @endif
                                            </td>
                                    <td class="px-3 py-2.5 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('admin.catalog-categories.show', $category) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                                        title="Ver categoria" aria-label="Ver categoria">
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-categories.edit', $category) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                                        title="Editar categoria" aria-label="Editar categoria">
                                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden xl:min-h-0 flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800">Productos y servicios dentro de {{ $catalogType->name }}</h3>
                            <p class="text-sm text-gray-400">Estos son los elementos que podrán mostrarse en la web o usarse en ventas.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
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

                    @if ($catalogType->items->isEmpty())
                        <div class="p-6 sm:p-8 text-center text-gray-500">
                            <p class="font-medium text-gray-700 mb-2">Todavía no hay productos o servicios en esta sección.</p>
                            <p class="text-sm mb-5">Empieza creando el primero para que luego se vea en la web o pueda venderse.</p>
                            <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                                Crear Primer Producto o Servicio
                            </a>
                        </div>
                    @else
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach ($catalogType->items as $item)
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 break-words">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-400 mt-1 break-words">{{ $item->category?->name ?: 'Sin categoria' }}</p>
                                        </div>
                                        @if ($item->active)
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Activo</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Oculto</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3 break-words">{{ $item->description ?: 'Sin descripcion adicional' }}</p>
                                    <div class="grid grid-cols-2 gap-3 text-sm mt-3">
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Precio</p>
                                            <p class="font-semibold text-gray-700">{{ $item->base_price !== null ? '$' . number_format((float) $item->base_price, 2) : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Presentaciones</p>
                                            <p class="font-semibold text-gray-700">{{ $item->variants_count }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-3">
                                        <a href="{{ route('admin.catalog-items.show', $item) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                            title="Ver producto o servicio" aria-label="Ver producto o servicio">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-items.edit', $item) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                            title="Editar producto o servicio" aria-label="Editar producto o servicio">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $item->id, 'catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
                                            title="Agregar presentacion" aria-label="Agregar presentacion">
                                            <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-y-auto overflow-x-hidden max-h-[320px] xl:max-h-none xl:min-h-0 flex-1">
                            <table class="w-full table-fixed text-sm text-left">
                                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                    <tr>
                                    <th class="w-[30%] px-3 py-2.5 text-gray-600 font-semibold text-center">Producto</th>
                                    <th class="w-[20%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden sm:table-cell">Categoria</th>
                                    <th class="w-[13%] px-3 py-2.5 text-gray-600 font-semibold text-center">Precio</th>
                                        <th class="w-[10%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden md:table-cell">Pres.</th>
                                    <th class="w-[12%] px-3 py-2.5 text-gray-600 font-semibold text-center">Estado</th>
                                        <th class="w-[15%] px-3 py-2.5 text-gray-600 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($catalogType->items as $item)
                                        <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 align-top text-center">
                                                <p class="font-semibold text-gray-800 truncate">{{ $item->name }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5 leading-tight line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($item->description, 55) ?: 'Sin descripcion adicional' }}
                                                </p>
                                            </td>
                                    <td class="px-3 py-2.5 text-gray-600 text-center truncate hidden sm:table-cell">{{ $item->category?->name ?: 'Sin categoria' }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-gray-800 text-center whitespace-nowrap">
                                                {{ $item->base_price !== null ? '$' . number_format((float) $item->base_price, 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-gray-700 text-center hidden md:table-cell">{{ $item->variants_count }}</td>
                                    <td class="px-3 py-2.5 text-center">
                                                @if ($item->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium">Activo</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium">Oculto</span>
                                                @endif
                                            </td>
                                    <td class="px-3 py-2.5 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('admin.catalog-items.show', $item) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                                        title="Ver producto o servicio" aria-label="Ver producto o servicio">
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-items.edit', $item) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                                        title="Editar producto o servicio" aria-label="Editar producto o servicio">
                                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $item->id, 'catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
                                                        title="Agregar presentacion" aria-label="Agregar presentacion">
                                                        <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
