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
    $typeLabel = $isWeb
        ? (($record->order_type ?? 'purchase') === 'reservation' ? 'Reserva web' : 'Compra web')
        : 'Venta directa';
    $firstItem = $items->first();
    $clientName = $record->user->name ?? 'Invitado';
    $attendedBy = $isWeb ? '-' : ($record->attendedBy->name ?? '-');
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

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs text-gray-600">{{ $isWeb ? 'Web' : 'Sistema' }}</span>
            <h2 class="text-2xl font-bold text-gray-900">{{ $isWeb ? 'Orden' : 'Venta' }} #{{ $record->id }}</h2>
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
            <a href="{{ route('admin.ventas.index') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
        <section class="xl:col-span-8 bg-white rounded-xl border border-gray-100 p-4 sm:p-5 shadow-sm">
            <h3 class="font-semibold text-gray-900 mb-4">Detallación de la Venta</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <article class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <x-heroicon-o-user-circle class="w-5 h-5 text-gray-500" />
                        <h4 class="font-semibold text-gray-800">Cliente</h4>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <p><span class="text-gray-500">Usuario:</span> {{ $clientName }}</p>
                    </div>
                </article>

                <article class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <x-heroicon-o-truck class="w-5 h-5 text-gray-500" />
                        <h4 class="font-semibold text-gray-800">Vehículo</h4>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        @php
                            $mainVehicle = $isWeb
                                ? ($firstItem?->vehicle_display ?? null)
                                : ($record->vehicle?->plate ?? $firstItem?->vehicle?->plate ?? $firstItem?->vehicleType?->name ?? null);
                        @endphp
                        <p><span class="text-gray-500">Vehículo:</span> {{ $mainVehicle ?: 'Sin vehículo' }}</p>
                    </div>
                </article>

                <article class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <x-heroicon-o-calendar-days class="w-5 h-5 text-gray-500" />
                        <h4 class="font-semibold text-gray-800">Fechas</h4>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700 space-y-1">
                        <p><span class="text-gray-500">Fecha de inicio:</span> {{ $record->created_at->format('d/m/Y H:i') }}</p>
                        <p><span class="text-gray-500">Fecha de fin:</span> {{ optional($record->updated_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </article>

                <article class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <x-heroicon-o-sparkles class="w-5 h-5 text-gray-500" />
                        <h4 class="font-semibold text-gray-800">Servicio / Producto</h4>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700 space-y-2">
                        @forelse($items as $item)
                            @php
                                $itemName = $isWeb ? $item->item_display_name : $item->name_snapshot;
                                $itemType = $isWeb ? $item->item_type_label : ($item->type_snapshot ?? '-');
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <p><span class="text-gray-500">Ítem:</span> {{ $itemName }}</p>
                                <p class="text-gray-500">{{ $itemType }}</p>
                            </div>
                        @empty
                            <p class="text-gray-400">Sin ítems registrados.</p>
                        @endforelse
                    </div>
                </article>

                <article class="md:col-span-2 rounded-lg border border-gray-200 overflow-hidden">
                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <x-heroicon-o-list-bullet class="w-5 h-5 text-gray-500" />
                        <h4 class="font-semibold text-gray-800">Importes Detallados</h4>
                    </div>
                    <div class="divide-y divide-gray-100">
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
                                $lineTotal = $isWeb ? $unitPrice * $quantity : (float) ($item->total ?? $item->subtotal);
                            @endphp
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 px-4 py-3 text-sm">
                                <div class="lg:col-span-4 min-w-0">
                                    <p class="font-semibold text-gray-800 break-words">{{ $itemName }}</p>
                                    <p class="text-xs text-gray-500 break-words">{{ $itemType }} · {{ $vehicleLabel }}</p>
                                </div>
                                <div class="lg:col-span-2 flex lg:block justify-between">
                                    <span class="text-gray-500">Cantidad:</span>
                                    <strong class="text-gray-800">{{ $quantity }}</strong>
                                </div>
                                <div class="lg:col-span-2 flex lg:block justify-between">
                                    <span class="text-gray-500">Unitario:</span>
                                    <strong class="text-gray-800">${{ number_format($unitPrice, 2) }}</strong>
                                </div>
                                <div class="lg:col-span-2 flex lg:block justify-between">
                                    <span class="text-gray-500">Impuesto:</span>
                                    <strong class="text-gray-800">${{ number_format($taxAmount, 2) }}</strong>
                                </div>
                                <div class="lg:col-span-2 flex lg:block justify-between">
                                    <span class="text-gray-500">Total:</span>
                                    <strong class="text-gray-900">${{ number_format($lineTotal, 2) }}</strong>
                                </div>
                            </div>
                        @empty
                            <p class="px-4 py-8 text-center text-gray-400 text-sm">Sin ítems registrados.</p>
                        @endforelse
                    </div>
                </article>

                @if(!$isWeb && $record->notes)
                    <article class="md:col-span-2 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="flex items-center gap-2 bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500" />
                            <h4 class="font-semibold text-gray-800">Notas internas</h4>
                        </div>
                        <p class="px-4 py-3 text-sm text-gray-700 whitespace-pre-line">{{ $record->notes }}</p>
                    </article>
                @endif
            </div>
        </section>

        <aside class="xl:col-span-4 space-y-4">
            <section class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
                <h3 class="font-semibold text-gray-900">Resumen Financiero</h3>
                <div class="text-sm">
                    <p class="text-gray-500">Estado</p>
                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <div class="text-sm">
                    <p class="text-gray-500">Tipo</p>
                    <p class="font-medium text-gray-800">{{ $typeLabel }}</p>
                </div>
                <div class="text-sm">
                    <p class="text-gray-500">Atendido por</p>
                    <p class="font-medium text-gray-800">{{ $attendedBy }}</p>
                </div>
                <div class="border-t border-gray-100 pt-3 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <strong>${{ number_format($record->subtotal ?? $record->total, 2) }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Descuentos</span>
                        <strong>${{ number_format($record->discount ?? 0, 2) }}</strong>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Impuestos</span>
                        <strong>${{ number_format($record->tax_total ?? 0, 2) }}</strong>
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($record->total, 2) }}</p>
                </div>
            </section>

            @unless($isWeb)
                <section class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
                    <h3 class="font-semibold text-gray-900">Pagos</h3>
                    @forelse($record->payments as $payment)
                        <div class="rounded-lg border border-gray-100 p-3 text-sm space-y-2">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Método:</span>
                                <strong>{{ $paymentLabels[$payment->method] ?? ucfirst((string) $payment->method) }}</strong>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Monto:</span>
                                <strong>${{ number_format($payment->amount, 2) }}</strong>
                            </div>
                            @if($payment->transaction_id || $payment->reference || $payment->authorization_code)
                                <div class="flex justify-between gap-3">
                                    <span class="text-gray-500">Transacción:</span>
                                    <strong class="text-right break-all">{{ $payment->transaction_id ?? $payment->reference ?? $payment->authorization_code }}</strong>
                                </div>
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
                </section>
            @endunless

            @if($isWeb && $record->transaction)
                <section class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-sm">
                    <h3 class="font-semibold text-gray-900 mb-2">Transacción</h3>
                    <a href="{{ route('admin.transactions.show', $record->transaction) }}" class="text-blue-600 hover:underline">Ver transacción</a>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection
