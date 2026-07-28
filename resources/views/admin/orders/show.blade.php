@extends('layouts.admin')

@section('title', 'Detalle Orden')

@section('content')
<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    @php
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-700',
            'reserved' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-600',
        ];

        $labels = [
            'pending' => 'Pendiente',
            'reserved' => 'Reservada',
            'paid' => 'Pagada',
            'failed' => 'Fallida',
            'cancelled' => 'Cancelada',
        ];

        $workDates = [
            'Cliente llego' => $order->arrived_at,
            'Inicio' => $order->started_at,
            'Listo' => $order->ready_at,
            'Completado' => $order->completed_at,
            'Cancelado' => $order->cancelled_at,
        ];
    @endphp

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800" title="Volver" aria-label="Volver">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Orden #{{ $order->id }}</h2>
            <p class="text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="ml-auto flex flex-wrap gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $labels[$order->status] ?? $order->status }}</span>
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $order->work_status_badge }}">{{ $order->work_status_label }}</span>
        </div>
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
                    <div class="flex justify-between">
                        <span class="text-gray-500">Trabajo</span>
                        <span class="font-semibold text-gray-700">{{ $order->work_status_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Responsable</span>
                        <span class="font-semibold text-gray-700">{{ $order->assignedTo?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Agenda</span>
                        <span class="font-semibold text-gray-700">{{ $order->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Venta comercial</span>
                        <span class="font-semibold text-gray-700">{{ $order->sale_id ? '#' . $order->sale_id : 'Pendiente' }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="font-semibold text-gray-700">Total</span>
                        <span class="text-xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Seguimiento operativo</p>
                <form method="POST" action="{{ route('admin.orders.operational-details', $order) }}" class="space-y-3 mb-5 pb-5 border-b border-gray-100">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Responsable</label>
                        <select name="assigned_to" class="w-full rounded-lg border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-0">
                            <option value="">Sin responsable</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" @selected((int) $order->assigned_to === (int) $worker->id)>{{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha programada</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ $order->scheduled_at?->format('Y-m-d\\TH:i') }}" class="w-full rounded-lg border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Notas operativas</label>
                        <textarea name="work_notes" rows="3" class="w-full rounded-lg border border-gray-200 bg-white py-2 px-3 text-sm text-gray-700 focus:border-gray-400 focus:outline-none focus:ring-0" placeholder="Observaciones internas del trabajo">{{ old('work_notes', $order->work_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-700 transition text-sm font-semibold">
                        Guardar datos operativos
                    </button>
                </form>

                <div class="space-y-3 text-sm">
                    @foreach($workDates as $label => $date)
                        @if($date)
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">{{ $label }}</span>
                                <span class="font-semibold text-gray-700">{{ $date->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    @endforeach

                    @if(!$order->arrived_at && !$order->started_at && !$order->ready_at && !$order->completed_at && !$order->cancelled_at)
                        <p class="text-gray-400">Sin movimientos operativos registrados.</p>
                    @endif

                    @if($order->work_notes)
                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold mb-1">Notas</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $order->work_notes }}</p>
                        </div>
                    @endif
                </div>

                @if($order->workTransitions())
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                        @foreach($order->workTransitions() as $nextStatus => $nextLabel)
                            <form method="POST" action="{{ route('admin.orders.work-status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="work_status" value="{{ $nextStatus }}">
                                <button type="submit" class="px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition text-xs font-semibold">
                                    {{ $nextLabel }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
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
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Vehiculo</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Cant.</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Precio unit.</th>
                            <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($order->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $item->item_display_name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $item->item_type_label }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $item->vehicle_display ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 font-semibold text-gray-800">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">Sin ítems registrados.</td>
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
