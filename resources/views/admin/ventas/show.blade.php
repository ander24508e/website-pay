@extends('layouts.admin')

@section('title', 'Detalle Venta')

@section('content')
@php
    $isWeb = $origin === 'web';
    $status = $record->status;
@endphp

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $isWeb ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $isWeb ? 'Web' : 'Sistema' }}
                </span>
                <h2 class="text-2xl font-bold text-gray-800">{{ $isWeb ? 'Orden' : 'Venta' }} #{{ $record->id }}</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Detalle comercial y operativo.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(!$isWeb && $record->status !== 'paid')
                <a href="{{ route('admin.ventas.edit', 'internal-' . $record->id) }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                    Editar
                </a>
            @endif
            @if($isWeb)
                <a href="{{ route('admin.orders.show', $record) }}" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
                    <x-heroicon-o-shopping-bag class="w-4 h-4" />
                    Ver orden
                </a>
            @endif
            <a href="{{ route('admin.ventas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm lg:col-span-2">
            <h3 class="font-semibold text-gray-800 mb-3">Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-center">Item</th>
                            <th class="px-3 py-2 text-center">Tipo</th>
                            <th class="px-3 py-2 text-center">Vehículo</th>
                            <th class="px-3 py-2 text-center">Cantidad</th>
                            <th class="px-3 py-2 text-center">Unitario</th>
                            <th class="px-3 py-2 text-center">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="border-t border-gray-100">
                                @if($isWeb)
                                    <td class="px-3 py-2 text-center text-gray-700">{{ $item->item_display_name }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->item_type_label }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->vehicle_display ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-3 py-2 text-center font-semibold text-gray-700">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                @else
                                    <td class="px-3 py-2 text-center text-gray-700">{{ $item->name_snapshot }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->type_snapshot ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->vehicle?->plate ?? $item->vehicleType?->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2 text-center text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-3 py-2 text-center font-semibold text-gray-700">${{ number_format($item->subtotal, 2) }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-400">Sin items registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
            <h3 class="font-semibold text-gray-800">Resumen</h3>
            @php
                $statusClass = [
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'reserved' => 'bg-blue-100 text-blue-700',
                    'paid' => 'bg-green-100 text-green-700',
                    'failed' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                ][$status] ?? 'bg-gray-100 text-gray-600';
                $statusLabel = [
                    'pending' => 'Pendiente',
                    'reserved' => 'Reservada',
                    'paid' => 'Pagada',
                    'failed' => 'Fallida',
                    'cancelled' => 'Cancelada',
                ][$status] ?? ucfirst((string) $status);
            @endphp
            <div class="text-sm">
                <p class="text-gray-500">Cliente</p>
                <p class="font-medium text-gray-800">{{ $record->user->name ?? 'Invitado' }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Estado</p>
                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Tipo</p>
                <p class="font-medium text-gray-800">{{ $isWeb ? (($record->order_type ?? 'purchase') === 'reservation' ? 'Reserva web' : 'Compra web') : 'Venta directa' }}</p>
            </div>
            @unless($isWeb)
                <div class="text-sm">
                    <p class="text-gray-500">Método de pago</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($record->payment_method) }}</p>
                </div>
                <div class="text-sm">
                    <p class="text-gray-500">Atendido por</p>
                    <p class="font-medium text-gray-800">{{ $record->attendedBy->name ?? '-' }}</p>
                </div>
            @endunless
            <div class="text-sm">
                <p class="text-gray-500">Fecha</p>
                <p class="font-medium text-gray-800">{{ $record->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @unless($isWeb)
                <div class="flex justify-between text-sm border-t border-gray-100 pt-3">
                    <span class="text-gray-500">Subtotal</span>
                    <strong>${{ number_format($record->subtotal, 2) }}</strong>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Descuento</span>
                    <strong>${{ number_format($record->discount, 2) }}</strong>
                </div>
            @endunless
            <div class="pt-3 border-t border-gray-100">
                <p class="text-gray-500 text-sm">Total</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($record->total, 2) }}</p>
            </div>
            @if($isWeb && $record->transaction)
                <div class="pt-3 border-t border-gray-100 text-sm">
                    <p class="text-gray-500">Transacción</p>
                    <a href="{{ route('admin.transactions.show', $record->transaction) }}" class="text-blue-600 hover:underline">Ver transacción</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
