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
        <a href="{{ route('admin.ventas.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold">+ Nueva Venta</a>
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

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left"># Orden</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Items</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3 font-semibold text-gray-700">#{{ $venta->id }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $venta->user->name ?? 'Invitado' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $venta->order_type ?? 'purchase' }}</td>
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3 text-gray-600">{{ $venta->items->count() }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.ventas.show', $venta) }}" class="text-blue-600 hover:text-blue-700 font-medium mr-3">Ver</a>
                                <a href="{{ route('admin.ventas.edit', $venta) }}" class="text-gray-700 hover:text-gray-900 font-medium mr-3">Editar</a>
                                <form method="POST" action="{{ route('admin.ventas.destroy', $venta) }}" class="inline" onsubmit="return confirm('¿Eliminar venta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                                </form>
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
