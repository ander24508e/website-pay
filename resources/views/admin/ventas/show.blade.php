@extends('layouts.admin')

@section('title', 'Detalle Venta')

@section('content')
@php
    $isWeb = $origin === 'web';
    $status = $record->status;
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

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $isWeb ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $isWeb ? 'Web' : 'Sistema' }}
                </span>
                <h2 class="text-2xl font-bold text-gray-800">{{ $isWeb ? 'Orden' : 'Venta' }} #{{ $record->id }}</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Detalle comercial y operativo de la venta.</p>
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
            <h3 class="font-semibold text-gray-800 mb-3">Ítems</h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-center">Ítem</th>
                            <th class="px-3 py-2 text-center">Tipo</th>
                            <th class="px-3 py-2 text-center">Vehículo</th>
                            <th class="px-3 py-2 text-center">Cantidad</th>
                            <th class="px-3 py-2 text-center">Unitario</th>
                            <th class="px-3 py-2 text-center">Impuesto</th>
                            <th class="px-3 py-2 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php
                                $itemName = $isWeb ? $item->item_display_name : $item->name_snapshot;
                                $itemType = $isWeb ? $item->item_type_label : ($item->type_snapshot ?? '-');
                                $vehicleLabel = $isWeb
                                    ? ($item->vehicle_display ?? '-')
                                    : ($item->vehicle?->plate ?? $item->vehicleType?->name ?? '-');
                                $quantity = (int) $item->quantity;
                                $unitPrice = (float) $item->unit_price;
                                $taxAmount = $isWeb ? 0 : (float) ($item->tax_amount ?? 0);
                                $lineTotal = $isWeb
                                    ? $unitPrice * $quantity
                                    : (float) ($item->total ?? $item->subtotal);
                            @endphp
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-center text-gray-700">
                                    <p class="font-medium">{{ $itemName }}</p>
                                    @unless($isWeb)
                                        @if($item->code_snapshot)
                                            <p class="text-xs text-gray-400">{{ $item->code_snapshot }}</p>
                                        @endif
                                    @endunless
                                </td>
                                <td class="px-3 py-2 text-center text-gray-500">{{ $itemType }}</td>
                                <td class="px-3 py-2 text-center text-gray-500">{{ $vehicleLabel }}</td>
                                <td class="px-3 py-2 text-center text-gray-500">{{ $quantity }}</td>
                                <td class="px-3 py-2 text-center text-gray-500">${{ number_format($unitPrice, 2) }}</td>
                                <td class="px-3 py-2 text-center text-gray-500">${{ number_format($taxAmount, 2) }}</td>
                                <td class="px-3 py-2 text-center font-semibold text-gray-700">${{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-gray-400">Sin ítems registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
                <h3 class="font-semibold text-gray-800">Resumen</h3>
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
                <div class="text-sm">
                    <p class="text-gray-500">Fecha</p>
                    <p class="font-medium text-gray-800">{{ $record->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @unless($isWeb)
                    <div class="text-sm">
                        <p class="text-gray-500">Atendido por</p>
                        <p class="font-medium text-gray-800">{{ $record->attendedBy->name ?? '-' }}</p>
                    </div>
                    @if($record->notes)
                        <div class="text-sm">
                            <p class="text-gray-500">Notas internas</p>
                            <p class="font-medium text-gray-800 whitespace-pre-line">{{ $record->notes }}</p>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm border-t border-gray-100 pt-3">
                        <span class="text-gray-500">Subtotal</span>
                        <strong>${{ number_format($record->subtotal, 2) }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Descuentos</span>
                        <strong>${{ number_format($record->discount, 2) }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Impuestos</span>
                        <strong>${{ number_format($record->tax_total ?? 0, 2) }}</strong>
                    </div>
                @endunless
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-gray-500 text-sm">Total</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($record->total, 2) }}</p>
                </div>
            </div>

            @unless($isWeb)
                @php
                    $paymentLabels = [
                        'cash' => 'Efectivo',
                        'payphone' => 'PayPhone',
                        'transfer' => 'Transferencia',
                        'card' => 'Tarjeta',
                        'credit' => 'Crédito',
                    ];
                    $paymentStatusLabels = [
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'failed' => 'Fallido',
                    ];
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
                    <h3 class="font-semibold text-gray-800">Pagos</h3>
                    @forelse($record->payments as $payment)
                        <div class="rounded-lg border border-gray-100 p-3 text-sm space-y-2">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Método</span>
                                <strong>{{ $paymentLabels[$payment->method] ?? ucfirst((string) $payment->method) }}</strong>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Estado</span>
                                <strong>{{ $paymentStatusLabels[$payment->status] ?? ucfirst((string) $payment->status) }}</strong>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Monto</span>
                                <strong>${{ number_format($payment->amount, 2) }}</strong>
                            </div>
                            @if($payment->received_amount)
                                <div class="flex justify-between gap-3">
                                    <span class="text-gray-500">Recibido</span>
                                    <strong>${{ number_format($payment->received_amount, 2) }}</strong>
                                </div>
                            @endif
                            @if($payment->change_amount)
                                <div class="flex justify-between gap-3">
                                    <span class="text-gray-500">Cambio</span>
                                    <strong>${{ number_format($payment->change_amount, 2) }}</strong>
                                </div>
                            @endif
                            @if($payment->transaction_id || $payment->reference || $payment->authorization_code)
                                <p class="text-xs text-gray-500 break-words">
                                    Ref: {{ $payment->transaction_id ?? $payment->reference ?? $payment->authorization_code }}
                                </p>
                            @endif
                            @if($payment->proof_path)
                                <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="inline-flex text-xs font-semibold text-blue-600 hover:underline">
                                    Ver comprobante
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Sin pagos registrados.</p>
                    @endforelse
                </div>
            @endunless

            @if($isWeb && $record->transaction)
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-sm">
                    <p class="text-gray-500">Transacción</p>
                    <a href="{{ route('admin.transactions.show', $record->transaction) }}" class="text-blue-600 hover:underline">Ver transacción</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
