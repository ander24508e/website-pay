@extends('layouts.admin')

@section('title', 'Presentaciones')

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
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Presentaciones</h2>
                    <p class="text-gray-500 text-sm mt-1">Tamaños, versiones o precios diferentes para productos y servicios.</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.catalog-variants.index') }}" class="flex-1 max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por presentación, SKU, producto o sección..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-variants.create') }}"
            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nueva Presentación
        </a>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Presentaciones</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Activas</p>
            <p class="text-2xl font-bold text-emerald-700 mt-2">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Base</p>
            <p class="text-2xl font-bold text-blue-700 mt-2">{{ $stats['default'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Con Stock</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['with_stock'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[1100px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Presentación</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Item</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Sección</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">SKU</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Precio</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Stock</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Flags</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($variants as $variant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-800">{{ $variant->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ trim(($variant->presentation ?? '') . ' ' . ($variant->specification ?? '')) ?: 'Sin presentacion adicional' }}
                                </p>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-700">{{ $variant->item->name ?? 'Sin item' }}</p>
                                <p class="text-xs text-gray-400">{{ $variant->item->category->name ?? '-' }}</p>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700">
                                {{ $variant->item->type->name ?? 'Sin sección' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 font-mono text-xs">
                                {{ $variant->sku ?: '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $variant->price !== null ? '$' . number_format((float) $variant->price, 2) : '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700">
                                {{ $variant->stock ?? '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($variant->active)
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activa</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculta</span>
                                    @endif
                                    @if($variant->is_default)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Principal</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.catalog-variants.show', $variant) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">Ver</a>
                                    <a href="{{ route('admin.catalog-variants.edit', $variant) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">Editar</a>
                                    <form method="POST" action="{{ route('admin.catalog-variants.destroy', $variant) }}" onsubmit="return confirm('¿Eliminar esta variante universal?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-400">No hay presentaciones registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($variants->hasPages())
            <div class="p-4 border-t">
                {{ $variants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
