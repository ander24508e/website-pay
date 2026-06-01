@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Inventario</h2>
            <p class="text-gray-500 text-sm mt-1">Control de stock por variantes en items que usan inventario.</p>
        </div>
        <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2">
            <form method="GET" action="{{ route('admin.inventario.index') }}" class="w-full lg:w-auto">
                <input type="search" name="q" value="{{ request('q') }}"
                       class="w-full lg:w-80 rounded-lg border border-gray-200 bg-white py-2.5 px-4 text-sm text-gray-700"
                       placeholder="Buscar por item, variante o SKU...">
            </form>
            <a href="{{ route('admin.inventario.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold text-center">+ Nuevo Movimiento</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Stock actual</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[820px] w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Variante</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3">Accion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($variants as $variant)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $variant->item->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $variant->item->type->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $variant->name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $variant->sku ?: '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ (int) ($variant->stock ?? 0) }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.inventario.movements.store') }}" class="flex flex-wrap gap-2 items-center">
                                        @csrf
                                        <input type="hidden" name="catalog_item_variant_id" value="{{ $variant->id }}">
                                        <select name="type" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs">
                                            <option value="in">Entrada</option>
                                            <option value="out">Salida</option>
                                            <option value="adjust">Ajustar</option>
                                        </select>
                                        <input type="number" name="quantity" min="1" required class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-xs" placeholder="Cant.">
                                        <button type="submit" class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-gray-700 transition">
                                            Guardar
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
                        <p class="text-sm font-semibold text-gray-800">{{ $movement->variant->item->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $movement->variant->name ?? '-' }}</p>
                        <p class="text-xs mt-1">
                            <span class="font-medium">{{ strtoupper($movement->type) }}</span>
                            · Cant: {{ $movement->quantity }}
                            · {{ $movement->stock_before ?? 0 }} -> {{ $movement->stock_after ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $movement->created_at?->format('d/m/Y H:i') }} · {{ $movement->user->name ?? 'Sistema' }}</p>
                        <div class="flex gap-3 mt-2">
                            <a href="{{ route('admin.inventario.movements.edit', $movement) }}" class="text-xs text-gray-700 hover:text-gray-900 font-medium">Editar</a>
                            <form method="POST" action="{{ route('admin.inventario.movements.destroy', $movement) }}" onsubmit="return confirm('¿Eliminar movimiento?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-400 text-sm">Sin movimientos.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
