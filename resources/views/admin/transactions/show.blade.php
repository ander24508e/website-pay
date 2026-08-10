@extends('layouts.admin')

@section('title', 'Detalle Transacción')

@section('content')
    <div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('admin.transactions.index') }}"
                class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800"
                title="Volver" aria-label="Volver">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h2 class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-gray-800">
                    <x-heroicon-o-credit-card class="w-6 h-6" />
                    Transacción #{{ $transaction->id }}
                </h2>
                <p class="text-gray-400 text-sm">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            </div>
            {{-- Badge estado --}}
            @php
                $badges = [
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-gray-100 text-gray-600',
                    'pending' => 'bg-yellow-100 text-yellow-700',
                ];
                $labels = [
                    'approved' => 'Aprobada',
                    'rejected' => 'Rechazada',
                    'cancelled' => 'Cancelada',
                    'pending' => 'Pendiente',
                ];
            @endphp
            <span
                class="ml-auto px-3 py-1.5 rounded-full text-xs font-semibold {{ $badges[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ $labels[$transaction->status] ?? $transaction->status }}
            </span>
        </div>

        {{-- LAYOUT DOS COLUMNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Columna izquierda --}}
            <div class="lg:col-span-1 flex flex-col gap-6">

                {{-- Card monto --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Monto</p>
                    <p class="text-4xl font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Procesado con Payphone</p>
                </div>

                {{-- Card cliente --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Cliente</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-lg flex-shrink-0">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $transaction->order?->user?->name ?? 'Invitado' }}</p>
                            <p class="text-xs text-gray-400">{{ $transaction->order?->user?->email ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card orden vinculada --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Orden Vinculada</p>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">ID Orden</span>
                            <span class="font-mono text-gray-700">#{{ $transaction->order_id }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total orden</span>
                            <span
                                class="font-semibold text-gray-800">${{ number_format($transaction->order?->total ?? $transaction->amount, 2) }}</span>
                        </div>
                    </div>
                    @if ($transaction->order)
                        <a href="{{ route('admin.orders.show', $transaction->order) }}"
                            class="flex items-center justify-center gap-1 bg-gray-50 text-gray-600 py-2 rounded-lg hover:bg-gray-100 transition text-xs font-medium">
                            Ver orden completa
                            <x-heroicon-o-arrow-right class="w-4 h-4" />
                        </a>
                    @endif
                </div>

            </div>

            {{-- Columna derecha --}}
            <div class="lg:col-span-2 flex flex-col gap-6">

                {{-- Card datos Payphone --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Datos de Payphone</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Referencia Payphone</p>
                            <p class="text-sm font-mono text-gray-700 break-all">{{ $transaction->payphone_ref ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Client Transaction ID</p>
                            <p class="text-sm font-mono text-gray-700 break-all">
                                {{ $transaction->client_transaction_id ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Estado</p>
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-medium {{ $badges[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $labels[$transaction->status] ?? $transaction->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Fecha de proceso</p>
                            <p class="text-sm text-gray-700">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                @if ($processingDetails->isNotEmpty())
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                            Detalles del procesamiento
                        </p>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach ($processingDetails as $label => $value)
                                <div class="min-w-0">
                                    <dt class="text-xs text-gray-400 mb-1">{{ $label }}</dt>
                                    <dd class="text-sm font-medium text-gray-700 break-words">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                {{-- Card metadatos --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Información del registro</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Creada</p>
                            <p class="text-sm text-gray-700 font-medium">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Última actualización</p>
                            <p class="text-sm text-gray-700 font-medium">
                                {{ $transaction->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
