@extends('layouts.admin')

@section('title', 'Transacciones')

@section('content')
<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-credit-card class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Transacciones</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Registro de todos los pagos procesados con Payphone</p>
        </div>

        <form method="GET" action="{{ route('admin.transactions.index') }}" class="w-full flex-1 xl:max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por transaccion, orden, cliente, ref. Payphone o estado..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <div class="bg-green-50 border border-green-200 px-4 py-2.5 rounded-lg">
            <p class="text-xs text-green-600 font-semibold uppercase tracking-wide">Total aprobado</p>
            <p class="text-xl font-bold text-green-700">
                ${{ number_format($transactions->where('status', 'approved')->sum('amount'), 2) }}
            </p>
        </div>
    </div>

    @php
        $statusBadges = [
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-600',
            'pending' => 'bg-yellow-100 text-yellow-700',
        ];
        $statusLabels = [
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'cancelled' => 'Cancelada',
            'pending' => 'Pendiente',
        ];
    @endphp

    <div class="md:hidden space-y-3">
        @forelse($transactions as $transaction)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-mono text-xs text-gray-400">#{{ $transaction->id }}</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $transaction->order->user->name ?? 'Invitado' }}</p>
                        <p class="text-xs text-gray-400 break-words">{{ $transaction->order->user->email ?? '—' }}</p>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$transaction->status] ?? $transaction->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Orden</p>
                        <a href="{{ route('admin.orders.show', $transaction->order) }}" class="text-blue-600 hover:underline font-mono text-xs">#{{ $transaction->order_id }}</a>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Monto</p>
                        <p class="font-semibold text-gray-800">${{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Ref. Payphone</p>
                        <p class="font-mono text-xs text-gray-700 break-words">{{ $transaction->payphone_ref ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Fecha</p>
                        <p class="text-gray-700">{{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" title="Ver transacción" aria-label="Ver transacción">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">
                <x-heroicon-o-credit-card class="w-12 h-12 mx-auto mb-3 opacity-50" />
                No hay transacciones registradas aún
            </div>
        @endforelse
    </div>

    <div class="hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-hidden overflow-y-auto max-h-[70vh]">
            <table class="w-full table-fixed text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center w-[8%]">#</th>
                        <th class="px-3 py-3 text-center w-[22%]">Cliente</th>
                        <th class="px-3 py-3 text-center w-[10%]">Orden</th>
                        <th class="px-3 py-3 text-center w-[11%]">Monto</th>
                        <th class="px-3 py-3 text-center w-[13%]">Estado</th>
                        <th class="px-3 py-3 text-center w-[18%]">Ref.</th>
                        <th class="px-3 py-3 text-center w-[12%]">Fecha</th>
                        <th class="px-3 py-3 text-center w-[8%]">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-center text-gray-400 font-mono text-xs truncate">#{{ $transaction->id }}</td>
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-800 truncate">{{ $transaction->order->user->name ?? 'Invitado' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $transaction->order->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <a href="{{ route('admin.orders.show', $transaction->order) }}" class="inline-flex items-center justify-center gap-1 text-blue-600 hover:underline text-xs font-mono">
                                    <x-heroicon-o-document-text class="w-3 h-3" />
                                    #{{ $transaction->order_id }}
                                </a>
                            </td>
                            <td class="px-3 py-3 text-center font-semibold text-gray-800 truncate">${{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$transaction->status] ?? $transaction->status }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center text-gray-500 font-mono text-xs truncate">{{ $transaction->payphone_ref ?? '—' }}</td>
                            <td class="px-3 py-3 text-center text-gray-500 text-xs truncate">{{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-3 text-center">
                                <a href="{{ route('admin.transactions.show', $transaction) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Ver transacción" aria-label="Ver transacción">
                                    <x-heroicon-o-eye class="w-4 h-4" />
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
    </div>

    @if($transactions->hasPages())
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
