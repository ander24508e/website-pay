@extends('layouts.admin')

@section('title', 'Detalle Orden')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    @php
        $badges = [
            'pending'   => 'bg-yellow-100 text-yellow-700',
            'reserved'  => 'bg-blue-100 text-blue-700',
            'paid'      => 'bg-green-100 text-green-700',
            'failed'    => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-600',
        ];

        $labels = [
            'pending'   => 'Pendiente',
            'reserved'  => 'Reservada',
            'paid'      => 'Pagada',
            'failed'    => 'Fallida',
            'cancelled' => 'Cancelada',
        ];
    @endphp

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">?</a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Orden #{{ $order->id }}</h2>
            <p class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="ml-auto px-3 py-1.5 rounded-full text-xs font-semibold {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $labels[$order->status] ?? $order->status }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Cliente</p>
                <p class="font-semibold text-gray-800">{{ $order->user->name ?? 'Invitado' }}</p>
                <p class="text-xs text-gray-400">{{ $order->user->email ?? '—' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Resumen</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipo</span>
                        <span class="font-semibold text-gray-700">{{ ($order->order_type ?? 'purchase') === 'reservation' ? 'Reserva' : 'Compra' }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="font-semibold text-gray-700">Total</span>
                        <span class="text-xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if(($order->order_type ?? 'purchase') === 'reservation' && $order->status !== 'paid')
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Pago manual</p>
                    <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                            Marcar reserva como pagada
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ítems de la orden</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Ítem</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Tipo</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Cant.</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Precio unit.</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($order->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $item->itemable->name ?? 'Ítem eliminado' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ str_contains($item->itemable_type, 'Product') ? 'Producto' : 'Servicio' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 font-semibold text-gray-800">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">Sin ítems registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($order->transaction)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Última transacción</p>
                    <p class="text-sm text-gray-700">Estado: <strong>{{ ucfirst($order->transaction->status) }}</strong></p>
                    <a href="{{ route('admin.transactions.show', $order->transaction) }}" class="inline-block mt-3 text-blue-600 hover:underline text-sm">Ver transacción</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
