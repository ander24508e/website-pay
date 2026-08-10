@extends('layouts.admin')

@section('title', 'Detalle Orden')

@push('styles')
    @vite('resources/scss/admin/orders-show.scss')
@endpush

@section('content')
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
            'Cliente llegó' => $order->arrived_at,
            'Inicio' => $order->started_at,
            'Listo' => $order->ready_at,
            'Completado' => $order->completed_at,
            'Cancelado' => $order->cancelled_at,
        ];

        $hasWorkDates = collect($workDates)->filter()->isNotEmpty();
    @endphp

    <div class="order-show-page">
        <header class="order-show-header">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800"
                    title="Volver" aria-label="Volver">
                    <x-heroicon-o-arrow-left class="h-5 w-5" />
                </a>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Orden #{{ $order->id }}</h2>
                        <span class="text-sm text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="mt-0.5 truncate text-xs text-gray-400">Seguimiento comercial y operativo de la orden</p>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap justify-end gap-2">
                <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $labels[$order->status] ?? $order->status }}
                </span>
                <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $order->work_status_badge }}">
                    {{ $order->work_status_label }}
                </span>
            </div>
        </header>

        <div class="order-show-layout">
            <aside class="order-show-sidebar">
                <section class="order-show-card order-show-overview">
                    <div class="order-show-card-header">
                        <div>
                            <h3>Información general</h3>
                            <p>Cliente y resumen de la orden</p>
                        </div>
                        <x-heroicon-o-user-circle class="h-5 w-5 text-gray-400" />
                    </div>

                    <div class="order-show-card-body order-show-overview-body">
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="font-semibold text-gray-800">{{ $order->user->name ?? 'Invitado' }}</p>
                            <p class="mt-0.5 break-all text-xs text-gray-400">{{ $order->user->email ?? 'Sin correo registrado' }}</p>
                        </div>

                        <dl class="order-show-summary-list">
                            <div>
                                <dt>Tipo</dt>
                                <dd>{{ ($order->order_type ?? 'purchase') === 'reservation' ? 'Reserva' : 'Compra' }}</dd>
                            </div>
                            <div>
                                <dt>Trabajo</dt>
                                <dd>{{ $order->work_status_label }}</dd>
                            </div>
                            <div>
                                <dt>Responsable</dt>
                                <dd>{{ $order->assignedTo?->name ?? 'Sin asignar' }}</dd>
                            </div>
                            <div>
                                <dt>Programada</dt>
                                <dd>{{ $order->scheduled_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}</dd>
                            </div>
                            <div>
                                <dt>Venta</dt>
                                <dd>{{ $order->sale_id ? '#' . $order->sale_id : 'Pendiente' }}</dd>
                            </div>
                        </dl>

                        <div class="order-show-total">
                            <span>Total</span>
                            <strong>${{ number_format($order->total, 2) }}</strong>
                        </div>
                    </div>
                </section>

                @if ($order->transaction)
                    <section class="order-show-card order-show-transaction">
                        <div class="order-show-card-header">
                            <div>
                                <h3>Última transacción</h3>
                                <p>Información del movimiento registrado</p>
                            </div>
                            <x-heroicon-o-credit-card class="h-5 w-5 text-gray-400" />
                        </div>
                        <div class="order-show-card-body flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400">Estado</p>
                                <p class="truncate font-semibold text-gray-800">{{ ucfirst($order->transaction->status) }}</p>
                            </div>
                            <a href="{{ route('admin.transactions.show', $order->transaction) }}"
                                class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 px-3 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                Ver transacción
                            </a>
                        </div>
                    </section>
                @endif
            </aside>

            <main class="order-show-main">
                <section class="order-show-card order-show-items">
                    <div class="order-show-card-header">
                        <div>
                            <h3>Ítems de la orden</h3>
                            <p>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'ítem registrado' : 'ítems registrados' }}</p>
                        </div>
                        <x-heroicon-o-shopping-bag class="h-5 w-5 text-gray-400" />
                    </div>

                    <div class="order-show-items-scroll">
                        <div class="order-show-mobile-items">
                            @forelse ($order->items as $item)
                                <article class="order-show-mobile-item">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="break-words font-semibold text-gray-800">{{ $item->item_display_name }}</p>
                                            <p class="mt-0.5 text-xs text-gray-400">{{ $item->item_type_label }}</p>
                                        </div>
                                        <strong class="shrink-0 text-gray-900">${{ number_format($item->unit_price * $item->quantity, 2) }}</strong>
                                    </div>
                                    <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                                        <div>
                                            <span>Vehículo</span>
                                            <strong>{{ $item->vehicle_display ?? '-' }}</strong>
                                        </div>
                                        <div>
                                            <span>Cantidad</span>
                                            <strong>{{ $item->quantity }}</strong>
                                        </div>
                                        <div>
                                            <span>Unitario</span>
                                            <strong>${{ number_format($item->unit_price, 2) }}</strong>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="p-8 text-center text-sm text-gray-400">Sin ítems registrados.</div>
                            @endforelse
                        </div>

                        <table class="order-show-table">
                            <thead>
                                <tr>
                                    <th>Ítem</th>
                                    <th>Tipo</th>
                                    <th>Vehículo</th>
                                    <th>Cant.</th>
                                    <th>Precio unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->item_display_name }}</td>
                                        <td>{{ $item->item_type_label }}</td>
                                        <td>{{ $item->vehicle_display ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-400">Sin ítems registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="order-show-lower-grid">
                    <section class="order-show-card order-show-operations">
                        <div class="order-show-card-header">
                            <div>
                                <h3>Seguimiento operativo</h3>
                                <p>Responsable, agenda y avance del trabajo</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->work_status_badge }}">
                                {{ $order->work_status_label }}
                            </span>
                        </div>

                        <div class="order-show-card-scroll">
                            <form method="POST" action="{{ route('admin.orders.operational-details', $order) }}"
                                class="order-show-operational-form">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="assignedTo">Responsable</label>
                                    <select id="assignedTo" name="assigned_to">
                                        <option value="">Sin responsable</option>
                                        @foreach ($workers as $worker)
                                            <option value="{{ $worker->id }}" @selected((int) $order->assigned_to === (int) $worker->id)>
                                                {{ $worker->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="scheduledAt">Fecha programada</label>
                                    <input id="scheduledAt" type="datetime-local" name="scheduled_at"
                                        value="{{ $order->scheduled_at?->format('Y-m-d\TH:i') }}">
                                </div>

                                <div class="order-show-notes-field">
                                    <label for="workNotes">Notas operativas</label>
                                    <textarea id="workNotes" name="work_notes" rows="2" placeholder="Observaciones internas del trabajo">{{ old('work_notes', $order->work_notes) }}</textarea>
                                </div>

                                <button type="submit">Guardar datos operativos</button>
                            </form>

                            <div class="order-show-history">
                                @if ($hasWorkDates)
                                    <div class="order-show-history-grid">
                                        @foreach ($workDates as $label => $date)
                                            @if ($date)
                                                <div>
                                                    <span>{{ $label }}</span>
                                                    <strong>{{ $date->format('d/m/Y H:i') }}</strong>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-400">Sin movimientos operativos registrados.</p>
                                @endif
                            </div>

                            @if ($order->workTransitions())
                                <div class="order-show-actions">
                                    @foreach ($order->workTransitions() as $nextStatus => $nextLabel)
                                        <form method="POST" action="{{ route('admin.orders.work-status', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="work_status" value="{{ $nextStatus }}">
                                            <button type="submit">{{ $nextLabel }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="order-show-card order-show-payment">
                        <div class="order-show-card-header">
                            <div>
                                <h3>Cobro</h3>
                                <p>Pago y cierre comercial de la orden</p>
                            </div>
                            <x-heroicon-o-banknotes class="h-5 w-5 text-gray-400" />
                        </div>

                        <div class="order-show-card-scroll">
                            @if ($order->status === 'paid')
                                <div class="order-show-payment-state order-show-payment-state--paid">
                                    <x-heroicon-o-check-circle class="h-8 w-8" />
                                    <div>
                                        <strong>Orden pagada</strong>
                                        <p>El ingreso ya fue registrado en ventas.</p>
                                    </div>
                                </div>
                            @elseif (($order->work_status ?? \App\Models\Order::WORK_PENDING) === \App\Models\Order::WORK_READY)
                                <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}"
                                    class="order-show-payment-form" data-order-payment-form data-total="{{ (float) $order->total }}">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label for="paymentMethod">Método de pago</label>
                                        <select id="paymentMethod" name="payment_method" required>
                                            <option value="cash">Efectivo</option>
                                            <option value="transfer">Transferencia</option>
                                            <option value="card">Tarjeta</option>
                                            <option value="payphone">PayPhone</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="receivedAmount">Monto recibido</label>
                                        <input id="receivedAmount" name="received_amount" type="number"
                                            min="{{ (float) $order->total }}" step="0.01"
                                            value="{{ old('received_amount', number_format((float) $order->total, 2, '.', '')) }}" required>
                                    </div>
                                    <div>
                                        <label for="paymentReference">Referencia <span>(opcional)</span></label>
                                        <input id="paymentReference" name="payment_reference" type="text"
                                            value="{{ old('payment_reference') }}">
                                    </div>
                                    <div class="order-show-change">
                                        <span>Cambio</span>
                                        <strong data-payment-change>$0.00</strong>
                                    </div>
                                    <button type="submit">Cobrar y completar</button>
                                </form>
                            @else
                                <div class="order-show-payment-state">
                                    <x-heroicon-o-clock class="h-8 w-8" />
                                    <div>
                                        <strong>Cobro pendiente</strong>
                                        <p>Se habilitará cuando la orden esté marcada como lista.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-order-payment-form]').forEach((form) => {
            const receivedInput = form.querySelector('[name="received_amount"]');
            const changeOutput = form.querySelector('[data-payment-change]');
            const total = Number(form.dataset.total || 0);

            const updateChange = () => {
                const received = Number(receivedInput.value || 0);
                changeOutput.textContent = `$${Math.max(0, received - total).toFixed(2)}`;
            };

            receivedInput.addEventListener('input', updateChange);
            updateChange();
        });
    </script>
@endpush
