@extends('layouts.admin')

@section('title', 'Detalle Orden')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            ←
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">🧾 Orden #{{ $order->id }}</h2>
            <p class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        {{-- Badge estado --}}
        @php
            $badges = [
                'pending'   => 'bg-yellow-100 text-yellow-700',
                'paid'      => 'bg-green-100 text-green-700',
                'failed'    => 'bg-red-100 text-red-700',
                'cancelled' => 'bg-gray-100 text-gray-600',
            ];
            $labels = [
                'pending'   => '⏳ Pendiente',
                'paid'      => '✅ Pagada',
                'failed'    => '❌ Fallida',
                'cancelled' => '🚫 Cancelada',
            ];
        @endphp
        <span class="ml-auto px-3 py-1.5 rounded-full text-xs font-semibold {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
            {{ $labels[$order->status] ?? $order->status }}
        </span>
    </div>

    {{-- LAYOUT DOS COLUMNAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna izquierda --}}
        <div class="lg:col-span-1 flex flex-col gap-6">

            {{-- Card cliente --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Cliente</p>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-lg flex-shrink-0">
                        👤
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $order->user->name ?? 'Invitado' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user->email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Card resumen --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Resumen</p>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-700">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t pt-3">
                        <span class="font-semibold text-gray-700">Total</span>
                        <span class="text-xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Card transacción --}}
            @if($order->transaction)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Transacción Payphone</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400">Referencia</p>
                        <p class="text-sm font-mono text-gray-700">{{ $order->transaction->payphone_ref ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Estado</p>
                        <p class="text-sm font-medium text-gray-700">{{ ucfirst($order->transaction->status) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Fecha de pago</p>
                        <p class="text-sm text-gray-700">{{ $order->transaction->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <a href="{{ route('admin.transactions.show', $order->transaction) }}"
                       class="block text-center mt-3 bg-gray-50 text-gray-600 py-2 rounded-lg hover:bg-gray-100 transition text-xs font-medium">
                        Ver transacción completa →
                    </a>
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Transacción</p>
                <p class="text-sm text-gray-400">Sin transacción registrada.</p>
            </div>
            @endif

        </div>

        {{-- Columna derecha --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Card ítems --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ítems de la Orden</p>
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
                            <td class="px-6 py-3 font-medium text-gray-800">
                                {{ $item->itemable->name ?? 'Ítem eliminado' }}
                            </td>
                            <td class="px-6 py-3">
                                @if(str_contains($item->itemable_type, 'Product'))
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">📦 Producto</span>
                                @else
                                    <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded text-xs font-medium">🛠️ Servicio</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-6 py-3 text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-3 font-semibold text-gray-800">
                                ${{ number_format($item->unit_price * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                Sin ítems registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card metadatos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Información del registro</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Creada</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Última actualización</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($order->payphone_transaction_id)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 mb-1">ID Transacción Payphone</p>
                        <p class="text-sm font-mono text-gray-700">{{ $order->payphone_transaction_id }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection