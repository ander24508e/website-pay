@extends('layouts.admin')

@section('title', 'Productos y Servicios')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.catalog.index') }}"
                    class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800"
                    title="Volver" aria-label="Volver">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Productos y Servicios</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        @if(isset($selectedCategory) && $selectedCategory)
                            Estas viendo los productos y servicios de la categoría {{ $selectedCategory->name }}.
                        @elseif(isset($selectedType) && $selectedType)
                            Estas viendo los productos y servicios de la sección {{ $selectedType->name }}.
                        @else
                            Administra lo que el negocio vende, reserva o muestra en la página web.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.catalog-items.index') }}" class="flex-1 max-w-xl">
            @if(isset($selectedType) && $selectedType)
                <input type="hidden" name="catalog_type_id" value="{{ $selectedType->id }}">
            @endif
            @if(isset($selectedCategory) && $selectedCategory)
                <input type="hidden" name="catalog_category_id" value="{{ $selectedCategory->id }}">
            @endif
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, sección, categoría o descripción..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-items.create', array_filter(['catalog_type_id' => $selectedType->id ?? null, 'catalog_category_id' => $selectedCategory->id ?? null, 'return_to_type' => isset($selectedType) && $selectedType ? 1 : null])) }}"
            class="inline-flex items-center justify-center bg-gray-900 text-white w-11 h-11 rounded-lg hover:bg-gray-700 transition"
            title="Nuevo producto o servicio" aria-label="Nuevo producto o servicio">
            <x-heroicon-o-plus class="w-5 h-5" />
        </a>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Productos/Servicios</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Activos</p>
            <p class="text-2xl font-bold text-emerald-700 mt-2">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Comprables</p>
            <p class="text-2xl font-bold text-blue-700 mt-2">{{ $stats['purchasable'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Reservables</p>
            <p class="text-2xl font-bold text-amber-700 mt-2">{{ $stats['reservable'] }}</p>
        </div>
    </div>

    @if((isset($selectedType) && $selectedType) || (isset($selectedCategory) && $selectedCategory))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap items-center gap-3">
            <span class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Contexto actual</span>
            @if(isset($selectedType) && $selectedType)
                <a href="{{ route('admin.catalog-types.show', $selectedType) }}" class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium hover:bg-gray-200 transition">
                    Sección: {{ $selectedType->name }}
                </a>
            @endif
            @if(isset($selectedCategory) && $selectedCategory)
                <a href="{{ route('admin.catalog-categories.show', $selectedCategory) }}" class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium hover:bg-gray-200 transition">
                    Categoría: {{ $selectedCategory->name }}
                </a>
            @endif
            <a href="{{ route('admin.catalog-items.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Limpiar filtro
            </a>
        </div>
    @endif

    <div class="md:hidden space-y-3">
        @forelse($items as $item)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start gap-3">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-14 h-14 rounded-lg object-cover bg-gray-50 border border-gray-200 shrink-0">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-800 break-words">{{ $item->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 break-words">{{ \Illuminate\Support\Str::limit($item->description, 90) ?: 'Sin descripción adicional' }}</p>
                    </div>
                    @if($item->active)
                        <span class="shrink-0 bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-medium">Activo</span>
                    @else
                        <span class="shrink-0 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium">Oculto</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Sección</p>
                        <p class="text-gray-700 break-words">{{ $item->type->name ?? 'Sin sección' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Categoría</p>
                        <p class="text-gray-700 break-words">{{ $item->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                        <p class="font-semibold text-gray-800">${{ number_format($item->display_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Presentaciones</p>
                        <p class="font-semibold text-gray-800">{{ $item->variants_count }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1">
                    @if($item->purchasable)
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Venta</span>
                    @endif
                    @if($item->reservable)
                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-xs font-medium">Reserva</span>
                    @endif
                    @if($item->featured)
                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded text-xs font-medium">Destacado</span>
                    @endif
                    @if($item->uses_inventory)
                        <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-xs font-medium">Stock</span>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.catalog-items.show', $item) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                        title="Ver producto o servicio" aria-label="Ver producto o servicio">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.catalog-items.edit', $item) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition"
                        title="Editar producto o servicio" aria-label="Editar producto o servicio">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </a>
                    <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este item universal?');">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition"
                            title="Eliminar producto o servicio" aria-label="Eliminar producto o servicio">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">No hay productos o servicios registrados</div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-hidden overflow-y-auto max-h-[70vh]">
            <table class="w-full table-fixed text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center w-[7%]">Img</th>
                        <th class="px-3 py-3 text-center w-[21%]">Nombre</th>
                        <th class="px-3 py-3 text-center w-[12%]">Sección</th>
                        <th class="px-3 py-3 text-center w-[12%]">Categoría</th>
                        <th class="px-3 py-3 text-center w-[9%]">Precio</th>
                        <th class="px-3 py-3 text-center w-[16%]">Flags</th>
                        <th class="px-3 py-3 text-center w-[7%]">Pres.</th>
                        <th class="px-3 py-3 text-center w-[8%]">Estado</th>
                        <th class="px-3 py-3 text-center w-[10%]">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-center">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-12 h-12 rounded object-cover bg-gray-50 border border-gray-200 mx-auto">
                            </td>
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-800 truncate">{{ $item->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ \Illuminate\Support\Str::limit($item->description, 70) ?: 'Sin descripción adicional' }}</p>
                            </td>
                            <td class="px-3 py-3 text-gray-700 text-center truncate">{{ $item->type->name ?? 'Sin sección' }}</td>
                            <td class="px-3 py-3 text-gray-500 text-center truncate">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-800 text-center truncate">${{ number_format($item->display_price, 2) }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-1">
                                    @if($item->purchasable)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Venta</span>
                                    @endif
                                    @if($item->reservable)
                                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-xs font-medium">Reserva</span>
                                    @endif
                                    @if($item->featured)
                                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded text-xs font-medium">Dest.</span>
                                    @endif
                                    @if($item->uses_inventory)
                                        <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-xs font-medium">Stock</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 font-semibold text-gray-800 text-center">{{ $item->variants_count }}</td>
                            <td class="px-3 py-3 text-center">
                                @if($item->active)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Activo</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">Oculto</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.catalog-items.show', $item) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                        title="Ver producto o servicio" aria-label="Ver producto o servicio">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.catalog-items.edit', $item) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                                        title="Editar producto o servicio" aria-label="Editar producto o servicio">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este item universal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition"
                                            title="Eliminar producto o servicio" aria-label="Eliminar producto o servicio">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-gray-400">No hay productos o servicios registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($items->hasPages())
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
