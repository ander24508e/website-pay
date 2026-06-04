@extends('layouts.admin')

@section('title', 'Ventas')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <div class="flex items-center gap-2">
            <x-heroicon-o-banknotes class="w-8 h-8 text-gray-800" />
            <h2 class="text-2xl font-bold text-gray-800">Ventas</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Control de ventas del negocio basado en ordenes y pagos.</p>
        </div>
        <a href="{{ route('admin.ventas.create') }}"
            class="inline-flex items-center justify-center bg-gray-900 text-white w-11 h-11 rounded-lg hover:bg-gray-700 transition"
            title="Nueva venta" aria-label="Nueva venta">
            <x-heroicon-o-plus class="w-5 h-5" />
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Ventas Cobradas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($stats['total_ventas'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Ordenes</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_ordenes'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Ordenes Pagadas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['ordenes_pagadas'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Ticket Promedio</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($stats['ticket_promedio'], 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por cliente, orden o estado" class="md:col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Todos los estados</option>
                @foreach (['pending' => 'Pendiente', 'paid' => 'Pagada', 'reserved' => 'Reservada', 'failed' => 'Fallida', 'cancelled' => 'Cancelada'] as $key => $label)
                    <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <div class="md:col-span-5 flex gap-2">
                <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Filtrar</button>
                <a href="{{ route('admin.ventas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="md:hidden space-y-3">
        @forelse($ventas as $venta)
            @php
                $statusClass = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'paid' => 'bg-green-100 text-green-700',
                    'reserved' => 'bg-blue-100 text-blue-700',
                    'failed' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                ][$venta->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800">#{{ $venta->id }}</p>
                        <p class="text-sm text-gray-500 break-words">{{ $venta->user->name ?? 'Invitado' }}</p>
                    </div>
                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($venta->status) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Tipo</p>
                        <p class="text-gray-700 break-words">{{ $venta->order_type ?? 'purchase' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Items</p>
                        <p class="text-gray-700">{{ $venta->items->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Total</p>
                        <p class="font-semibold text-gray-800">${{ number_format($venta->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Fecha</p>
                        <p class="text-gray-700">{{ $venta->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.ventas.show', $venta) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                        title="Ver venta" aria-label="Ver venta">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.ventas.edit', $venta) }}"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition"
                        title="Editar venta" aria-label="Editar venta">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </a>
                    <form method="POST" action="{{ route('admin.ventas.destroy', $venta) }}" onsubmit="return confirm('¿Eliminar venta?');">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition"
                            title="Eliminar venta" aria-label="Eliminar venta">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">No hay ventas registradas.</div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-hidden">
            <table class="w-full table-fixed text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-center w-[10%]"># Orden</th>
                        <th class="px-4 py-3 text-center w-[22%]">Cliente</th>
                        <th class="px-4 py-3 text-center w-[12%]">Tipo</th>
                        <th class="px-4 py-3 text-center w-[13%]">Estado</th>
                        <th class="px-4 py-3 text-center w-[8%]">Items</th>
                        <th class="px-4 py-3 text-center w-[12%]">Total</th>
                        <th class="px-4 py-3 text-center w-[13%]">Fecha</th>
                        <th class="px-4 py-3 text-center w-[10%]">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-center truncate">#{{ $venta->id }}</td>
                            <td class="px-4 py-3 text-gray-600 text-center truncate">{{ $venta->user->name ?? 'Invitado' }}</td>
                            <td class="px-4 py-3 text-gray-600 text-center truncate">{{ $venta->order_type ?? 'purchase' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'paid' => 'bg-green-100 text-green-700',
                                        'reserved' => 'bg-blue-100 text-blue-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                    ][$venta->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($venta->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-center">{{ $venta->items->count() }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 text-center truncate">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-4 py-3 text-gray-500 text-center truncate">{{ $venta->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.ventas.show', $venta) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                        title="Ver venta" aria-label="Ver venta">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.ventas.edit', $venta) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                                        title="Editar venta" aria-label="Editar venta">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.ventas.destroy', $venta) }}" onsubmit="return confirm('¿Eliminar venta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition"
                                            title="Eliminar venta" aria-label="Eliminar venta">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $ventas->links() }}
    </div>
</div>
@endsection
