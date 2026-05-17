@extends('layouts.admin')

@section('title', 'Items Universales')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.catalog.index') }}"
                    class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                    <span aria-hidden="true">&larr;</span>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Items Universales</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        @if(isset($selectedCategory) && $selectedCategory)
                            Estas viendo los items de la categoria {{ $selectedCategory->name }}.
                        @elseif(isset($selectedType) && $selectedType)
                            Estas viendo los items del catalogo {{ $selectedType->name }}.
                        @else
                            Base futura del catalogo publico para cualquier rubro de negocio.
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
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, tipo, categoria o descripcion..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-items.create', array_filter(['catalog_type_id' => $selectedType->id ?? null, 'catalog_category_id' => $selectedCategory->id ?? null, 'return_to_type' => isset($selectedType) && $selectedType ? 1 : null])) }}"
            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nuevo Item
        </a>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Items</p>
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
                    Catalogo: {{ $selectedType->name }}
                </a>
            @endif
            @if(isset($selectedCategory) && $selectedCategory)
                <a href="{{ route('admin.catalog-categories.show', $selectedCategory) }}" class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium hover:bg-gray-200 transition">
                    Categoria: {{ $selectedCategory->name }}
                </a>
            @endif
            <a href="{{ route('admin.catalog-items.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Limpiar filtro
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[980px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Imagen</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Tipo</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Categoria</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Precio</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Flags</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Variantes</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-12 h-12 rounded object-cover bg-gray-50 border border-gray-200">
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-800">{{ $item->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($item->description, 70) ?: 'Sin descripcion adicional' }}</p>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700">
                                {{ $item->type->name ?? 'Sin tipo' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                {{ $item->category->name ?? '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                ${{ number_format($item->display_price, 2) }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($item->purchasable)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Compra</span>
                                    @endif
                                    @if($item->reservable)
                                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-xs font-medium">Reserva</span>
                                    @endif
                                    @if($item->featured)
                                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded text-xs font-medium">Destacado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $item->variants_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @if($item->active)
                                    <span class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.catalog-items.show', $item) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">Ver</a>
                                    <a href="{{ route('admin.catalog-items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">Editar</a>
                                    <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este item universal?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-gray-400">No hay items universales registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
