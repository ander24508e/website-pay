@extends('layouts.admin')

@section('title', 'Presentaciones')

@section('content')
<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.catalog.index') }}"
                    class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800"
                    title="Volver" aria-label="Volver">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Presentaciones</h2>
                    <p class="text-gray-500 text-sm mt-1">Tamaños, versiones o precios diferentes para productos y servicios.</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.catalog-variants.index') }}" class="w-full flex-1 lg:max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por presentación, SKU, producto o sección..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-variants.create') }}"
            class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-gray-900 text-white transition hover:bg-gray-700 sm:w-11"
            title="Nueva presentación" aria-label="Nueva presentación">
            <x-heroicon-o-plus class="w-5 h-5" />
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

    <div class="md:hidden space-y-3">
        @forelse($variants as $variant)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 break-words">{{ $variant->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 break-words">
                            @php
                                $variantPresentation = isset($variant->presentation) ? $variant->presentation : '';
                                $variantSpecification = isset($variant->specification) ? $variant->specification : '';
                                $variantDetails = trim($variantPresentation . ' ' . $variantSpecification);
                            @endphp
                            @if($variantDetails !== '')
                                {{ $variantDetails }}
                            @else
                                Sin presentación adicional
                            @endif
                        </p>
                    </div>
                    <div class="shrink-0 flex flex-col items-end gap-1">
                        @if($variant->active)
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activa</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculta</span>
                        @endif
                        @if($variant->is_default)
                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Principal</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Ítem</p>
                        <p class="text-gray-700 break-words">{{ $variant->item?->name ?? 'Sin ítem' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Categoría</p>
                        <p class="text-gray-700 break-words">{{ $variant->item?->category?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Sección</p>
                        <p class="text-gray-700 break-words">{{ $variant->item?->type?->name ?? 'Sin sección' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">SKU</p>
                        <p class="font-mono text-xs text-gray-700 break-words">{{ $variant->sku ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                        <p class="font-semibold text-gray-800">{{ $variant->price !== null ? '$' . number_format((float) $variant->price, 2) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Stock</p>
                        <p class="text-gray-700">{{ $variant->stock ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.catalog-variants.show', $variant) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                        title="Ver presentación" aria-label="Ver presentación">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.catalog-variants.edit', $variant) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition"
                        title="Editar presentación" aria-label="Editar presentación">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </a>
                    <form method="POST" action="{{ route('admin.catalog-variants.destroy', $variant) }}" onsubmit="return confirm('¿Eliminar esta variante universal?');">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition"
                            title="Eliminar presentación" aria-label="Eliminar presentación">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">No hay presentaciones registradas</div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-hidden overflow-y-auto max-h-[70vh]">
            <table class="w-full table-fixed text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center w-[20%]">Presentación</th>
                        <th class="px-3 py-3 text-center w-[18%]">Ítem</th>
                        <th class="px-3 py-3 text-center w-[13%]">Sección</th>
                        <th class="px-3 py-3 text-center w-[12%]">SKU</th>
                        <th class="px-3 py-3 text-center w-[10%]">Precio</th>
                        <th class="px-3 py-3 text-center w-[8%]">Stock</th>
                        <th class="px-3 py-3 text-center w-[11%]">Estado</th>
                        <th class="px-3 py-3 text-center w-[10%]">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($variants as $variant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-800 truncate">{{ $variant->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">
                                    @php
                                        $variantPresentation = isset($variant->presentation) ? $variant->presentation : '';
                                        $variantSpecification = isset($variant->specification) ? $variant->specification : '';
                                        $variantDetails = trim($variantPresentation . ' ' . $variantSpecification);
                                    @endphp
                                    @if($variantDetails !== '')
                                        {{ $variantDetails }}
                                    @else
                                        Sin presentación adicional
                                    @endif
                                </p>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-700 truncate">{{ $variant->item?->name ?? 'Sin ítem' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $variant->item?->category?->name ?? '-' }}</p>
                            </td>
                            <td class="px-3 py-3 text-gray-700 text-center truncate">{{ $variant->item?->type?->name ?? 'Sin sección' }}</td>
                            <td class="px-3 py-3 text-gray-500 text-center font-mono text-xs truncate">{{ $variant->sku ?: '-' }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-800 text-center truncate">{{ $variant->price !== null ? '$' . number_format((float) $variant->price, 2) : '-' }}</td>
                            <td class="px-3 py-3 text-gray-700 text-center">{{ $variant->stock ?? '-' }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-1">
                                    @if($variant->active)
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activa</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculta</span>
                                    @endif
                                    @if($variant->is_default)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Base</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.catalog-variants.show', $variant) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                        title="Ver presentación" aria-label="Ver presentación">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.catalog-variants.edit', $variant) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                                        title="Editar presentación" aria-label="Editar presentación">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.catalog-variants.destroy', $variant) }}" onsubmit="return confirm('¿Eliminar esta variante universal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition"
                                            title="Eliminar presentación" aria-label="Eliminar presentación">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
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
    </div>

    @if($variants->hasPages())
        <div class="mt-4">
            {{ $variants->links() }}
        </div>
    @endif
</div>
@endsection
