@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
@php
    $isProductContext = isset($selectedType) && $selectedType && (($selectedType->business_model ?? 'services') === \App\Models\CatalogType::BUSINESS_MODEL_PRODUCTS);
    $itemPlural = isset($selectedType) && $selectedType ? ($isProductContext ? 'productos' : 'servicios') : 'productos y servicios';
@endphp

<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.catalog.index') }}"
                    class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                    <span aria-hidden="true">&larr;</span>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Categorías</h2>
                    <p class="text-gray-500 text-sm mt-1">
                        @if(isset($selectedType) && $selectedType)
                            Estás viendo las Categorías de la Sección {{ $selectedType->name }}.
                        @else
                            Organiza {{ $itemPlural }} dentro de cada sección.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.catalog-categories.index') }}" class="flex-1 max-w-xl">
            @if(isset($selectedType) && $selectedType)
                <input type="hidden" name="catalog_type_id" value="{{ $selectedType->id }}">
            @endif
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, sección, slug o descripción..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-categories.create', array_filter(['catalog_type_id' => isset($selectedType) && $selectedType ? $selectedType->id : null, 'return_to_type' => isset($selectedType) && $selectedType ? 1 : null])) }}"
            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nueva Categoría
        </a>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Categorías</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Activas</p>
            <p class="text-2xl font-bold text-emerald-700 mt-2">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Con Ítems</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['with_items'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Subnegocios</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['types'] }}</p>
        </div>
    </div>

    @if(isset($selectedType) && $selectedType)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap items-center gap-3">
            <span class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Contexto actual</span>
            <a href="{{ route('admin.catalog-types.show', $selectedType) }}" class="inline-flex items-center rounded-full bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium hover:bg-gray-200 transition">
                Catálogo: {{ $selectedType->name }}
            </a>
            <a href="{{ route('admin.catalog-categories.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Limpiar filtro
            </a>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[840px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Tipo</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Slug</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Orden</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Ítems</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-800">{{ $category->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($category->description, 70) ?: 'Sin descripción adicional' }}</p>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $category->type?->name ?? 'Sin sección' }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 font-mono text-xs">
                                {{ $category->slug ?: '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $category->items_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @if($category->active)
                                    <span class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Activa</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Oculta</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.catalog-categories.show', $category) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">Ver</a>
                                    <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $category->catalog_type_id, 'catalog_category_id' => $category->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-2 py-1">Ítems</a>
                                    <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $category->catalog_type_id, 'catalog_category_id' => $category->id, 'return_to_type' => 1]) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium px-2 py-1">+ Ítem</a>
                                    <a href="{{ route('admin.catalog-categories.edit', $category) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">Editar</a>
                                    <form method="POST" action="{{ route('admin.catalog-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría universal?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400">No hay Categorías Registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection