@extends('layouts.admin')

@section('title', 'Transacciones')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-credit-card class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Transacciones</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Registro de todos los pagos procesados con Payphone</p>
        </div>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex-1 max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por transaccion, orden, cliente, ref. Payphone o estado..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>
        {{-- Ingreso total --}}
        <div class="bg-green-50 border border-green-200 px-4 py-2.5 rounded-lg">
            <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Total aprobado</p>
            <p class="text-xl font-bold text-green-700">
                ${{ number_format($transactions->where('status', 'approved')->sum('amount'), 2) }}
            </p>
        </div>
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[750px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">#</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Cliente</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Orden</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Monto</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Ref. Payphone</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Fecha</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">

                            {{-- ID --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-400 font-mono text-xs">
                                #{{ $transaction->id }}
                            </td>

                            {{-- Cliente --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $transaction->order->user->name ?? 'Invitado' }}</p>
                                    <p class="text-xs text-gray-400">{{ $transaction->order->user->email ?? '—' }}</p>
                                </div>
                            </td>

                            {{-- Orden --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <a href="{{ route('admin.orders.show', $transaction->order) }}"
                                   class="text-blue-600 hover:underline text-xs font-mono flex items-center gap-1">
                                    <x-heroicon-o-document-text class="w-3 h-3" />
                                    #{{ $transaction->order_id }}
                                </a>
                            </td>

                            {{-- Monto --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                ${{ number_format($transaction->amount, 2) }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @php
                                    $badges = [
                                        'approved'  => 'bg-green-100 text-green-700',
                                        'rejected'  => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                    ];
                                    $icons = [
                                        'approved'  => '<x-heroicon-o-check-circle class="w-3 h-3 inline mr-1" />',
                                        'rejected'  => '<x-heroicon-o-x-circle class="w-3 h-3 inline mr-1" />',
                                        'cancelled' => '<x-heroicon-o-ban class="w-3 h-3 inline mr-1" />',
                                        'pending'   => '<x-heroicon-o-clock class="w-3 h-3 inline mr-1" />',
                                    ];
                                    $labels = [
                                        'approved'  => 'Aprobada',
                                        'rejected'  => 'Rechazada',
                                        'cancelled' => 'Cancelada',
                                        'pending'   => 'Pendiente',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $badges[$transaction->status] ?? 'bg-gray-100 text-gray-600' }} flex items-center gap-1 w-fit">
                                    {!! $icons[$transaction->status] ?? '<x-heroicon-o-question-mark-circle class="w-3 h-3 inline mr-1" />' !!}
                                    {{ $labels[$transaction->status] ?? $transaction->status }}
                                </span>
                            </td>

                            {{-- Ref Payphone --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 font-mono text-xs">
                                {{ $transaction->payphone_ref ?? '—' }}
                            </td>

                            {{-- Fecha --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 text-xs">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 flex items-center gap-1">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                    Ver detalle
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                <x-heroicon-o-credit-card class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                No hay transacciones registradas aún
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        @if($transactions->hasPages())
            <div class="p-4 border-t">
                {{ $transactions->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
