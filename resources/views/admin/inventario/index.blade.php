@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-archive-box class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Inventario</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Control de stock por variantes en items que usan inventario.</p>
        </div>
        <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2">
            <form method="GET" action="{{ route('admin.inventario.index') }}" class="w-full lg:w-auto">
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input type="search" name="q" value="{{ request('q') }}"
                        class="w-full lg:w-80 rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700"
                        placeholder="Buscar por item, variante o SKU...">
                </div>
            </form>
            <a href="{{ route('admin.inventario.create') }}"
                class="inline-flex items-center justify-center bg-gray-900 text-white w-11 h-11 rounded-lg hover:bg-gray-700 transition"
                title="Nuevo movimiento" aria-label="Nuevo movimiento">
                <x-heroicon-o-plus class="w-5 h-5" />
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Stock actual</h3>
            </div>

            <div class="md:hidden space-y-3 p-4">
                @forelse($variants as $variant)
                    <div class="rounded-xl border border-gray-100 p-4 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 break-words">{{ $variant->item->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400 break-words">{{ $variant->item->type->name ?? '-' }}</p>
                            </div>
                            <span class="shrink-0 bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                Stock: {{ (int) ($variant->stock ?? 0) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">Variante</p>
                                <p class="text-gray-700 break-words">{{ $variant->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">SKU</p>
                                <p class="font-mono text-xs text-gray-700 break-words">{{ $variant->sku ?: '-' }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.inventario.movements.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-center">
                            @csrf
                            <input type="hidden" name="catalog_item_variant_id" value="{{ $variant->id }}">
                            <select name="type" class="border border-gray-200 rounded-lg px-2 py-2 text-xs">
                                <option value="in">Entrada</option>
                                <option value="out">Salida</option>
                                <option value="adjust">Ajustar</option>
                            </select>
                            <input type="number" name="quantity" min="1" required class="border border-gray-200 rounded-lg px-2 py-2 text-xs" placeholder="Cant.">
                            <button type="submit" class="inline-flex items-center justify-center bg-gray-900 text-white h-9 rounded-lg hover:bg-gray-700 transition sm:col-span-2" title="Guardar movimiento" aria-label="Guardar movimiento">
                                <x-heroicon-o-check class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-400">No hay variantes con inventario habilitado.</div>
                @endforelse
            </div>

            <div class="hidden md:block overflow-x-hidden">
                <table class="w-full table-fixed text-sm text-left">
                    <thead class="bg-gray-50 border-b text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-3 text-center w-[24%]">Item</th>
                            <th class="px-3 py-3 text-center w-[18%]">Variante</th>
                            <th class="px-3 py-3 text-center w-[14%]">SKU</th>
                            <th class="px-3 py-3 text-center w-[10%]">Stock</th>
                            <th class="px-3 py-3 text-center w-[34%]">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($variants as $variant)
                            <tr>
                                <td class="px-3 py-3 text-center">
                                    <p class="font-medium text-gray-800 truncate">{{ $variant->item->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $variant->item->type->name ?? '-' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center text-gray-700 truncate">{{ $variant->name }}</td>
                                <td class="px-3 py-3 text-center text-gray-500 font-mono text-xs truncate">{{ $variant->sku ?: '-' }}</td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-800">{{ (int) ($variant->stock ?? 0) }}</td>
                                <td class="px-3 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.inventario.movements.store') }}" class="flex flex-wrap justify-center gap-2 items-center">
                                        @csrf
                                        <input type="hidden" name="catalog_item_variant_id" value="{{ $variant->id }}">
                                        <select name="type" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                            <option value="in">Entrada</option>
                                            <option value="out">Salida</option>
                                            <option value="adjust">Ajustar</option>
                                        </select>
                                        <input type="number" name="quantity" min="1" required class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-center" placeholder="Cant.">
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition" title="Guardar movimiento" aria-label="Guardar movimiento">
                                            <x-heroicon-o-check class="w-4 h-4" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">No hay variantes con inventario habilitado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($variants->hasPages())
                <div class="p-4 border-t">{{ $variants->links() }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Movimientos recientes</h3>
            </div>
            <div class="max-h-[560px] overflow-y-auto">
                @forelse($recentMovements as $movement)
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 break-words">{{ $movement->variant->item->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 break-words">{{ $movement->variant->name ?? '-' }}</p>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <a href="{{ route('admin.inventario.movements.edit', $movement) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition" title="Editar movimiento" aria-label="Editar movimiento">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('admin.inventario.movements.destroy', $movement) }}" onsubmit="return confirm('¿Eliminar movimiento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition" title="Eliminar movimiento" aria-label="Eliminar movimiento">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs mt-2 text-center">
                            <span class="font-medium">{{ strtoupper($movement->type) }}</span>
                            · Cant: {{ $movement->quantity }}
                            · {{ $movement->stock_before ?? 0 }} → {{ $movement->stock_after ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1 text-center">{{ $movement->created_at?->format('d/m/Y H:i') }} · {{ $movement->user->name ?? 'Sistema' }}</p>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-400 text-sm">Sin movimientos.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
