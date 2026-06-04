@extends('layouts.admin')

@section('title', 'Ordenes')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Ordenes</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Historial de compras y reservas</p>
        </div>

        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex-1 max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por orden, cliente, correo, tipo o estado..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <div class="flex gap-3 flex-wrap">
            <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <x-heroicon-o-clock class="w-3 h-3" />
                Pendientes: {{ $orders->where('status', 'pending')->count() }}
            </span>
            <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <x-heroicon-o-calendar-days class="w-3 h-3" />
                Reservas: {{ $orders->where('order_type', 'reservation')->count() }}
            </span>
            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <x-heroicon-o-check-circle class="w-3 h-3" />
                Pagadas: {{ $orders->where('status', 'paid')->count() }}
            </span>
        </div>
    </div>

    @php
        $statusBadges = [
            'pending' => 'bg-yellow-100 text-yellow-700',
            'reserved' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-600',
        ];
        $statusLabels = [
            'pending' => 'Pendiente',
            'reserved' => 'Reservada',
            'paid' => 'Pagada',
            'failed' => 'Fallida',
            'cancelled' => 'Cancelada',
        ];
    @endphp

    <div class="md:hidden space-y-3">
        @forelse($orders as $order)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-mono text-xs text-gray-400">#{{ $order->id }}</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $order->user->name ?? 'Invitado' }}</p>
                        <p class="text-xs text-gray-400 break-words">{{ $order->user->email ?? '' }}</p>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Total</p>
                        <p class="font-semibold text-gray-800">${{ number_format($order->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Tipo</p>
                        <p class="text-gray-700">{{ ($order->order_type ?? 'purchase') === 'reservation' ? 'Reserva' : 'Compra' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs uppercase text-gray-400 font-semibold">Fecha</p>
                        <p class="text-gray-700">{{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" title="Ver orden" aria-label="Ver orden">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                    @if(($order->order_type ?? 'purchase') === 'reservation' && $order->status !== 'paid')
                        <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition" title="Marcar pagada" aria-label="Marcar pagada">
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('¿Eliminar esta orden? Esta accion no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Eliminar orden" aria-label="Eliminar orden">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">No hay ordenes registradas</div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-hidden overflow-y-auto max-h-[70vh]">
            <table class="w-full table-fixed text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center w-[8%]">#</th>
                        <th class="px-3 py-3 text-center w-[24%]">Cliente</th>
                        <th class="px-3 py-3 text-center w-[12%]">Total</th>
                        <th class="px-3 py-3 text-center w-[11%]">Tipo</th>
                        <th class="px-3 py-3 text-center w-[13%]">Estado</th>
                        <th class="px-3 py-3 text-center w-[17%]">Fecha</th>
                        <th class="px-3 py-3 text-center w-[15%]">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-center text-gray-400 font-mono text-xs truncate">#{{ $order->id }}</td>
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-800 truncate">{{ $order->user->name ?? 'Invitado' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $order->user->email ?? '' }}</p>
                            </td>
                            <td class="px-3 py-3 text-center font-semibold text-gray-800 truncate">${{ number_format($order->total, 2) }}</td>
                            <td class="px-3 py-3 text-center">
                                @if(($order->order_type ?? 'purchase') === 'reservation')
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Reserva</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium">Compra</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-gray-500 text-xs truncate">{{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Ver orden" aria-label="Ver orden">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    @if(($order->order_type ?? 'purchase') === 'reservation' && $order->status !== 'paid')
                                        <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-green-600 hover:bg-green-50 transition" title="Marcar pagada" aria-label="Marcar pagada">
                                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('¿Eliminar esta orden? Esta accion no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition" title="Eliminar orden" aria-label="Eliminar orden">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">No hay ordenes registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
