@extends('layouts.admin')

@section('title', 'Órdenes')

@push('styles')
    @vite('resources/scss/admin/orders-index.scss')
@endpush

@section('content')
@php
    $activeFilterCount = collect(['q', 'work_status', 'assigned_to', 'date_filter', 'date_from', 'date_to'])
        ->filter(fn ($filter) => request()->filled($filter))
        ->count();
@endphp

<div class="orders-index-page" x-data="{ filtersOpen: false, dateFilter: @js($dateFilter ?? '') }">
    <div class="orders-index-header flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="w-8 h-8 text-gray-800" />
                <h2 class="text-2xl font-bold text-gray-800">Órdenes</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Agenda operativa: pagos, reservas y trabajos del negocio.</p>
        </div>

        <button type="button" @click="filtersOpen = !filtersOpen"
            class="orders-filter-toggle relative items-center justify-center bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition"
            :aria-expanded="filtersOpen.toString()"
            :title="filtersOpen ? 'Ocultar filtros' : 'Mostrar filtros'"
            :aria-label="filtersOpen ? 'Ocultar filtros' : 'Mostrar filtros'">
            <span x-text="filtersOpen ? 'Ocultar filtros' : 'Mostrar filtros'">Mostrar filtros</span>
            @if($activeFilterCount > 0)
                <span class="orders-filter-count">{{ $activeFilterCount }}</span>
            @endif
        </button>
    </div>

    <div class="orders-index-stats grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase font-semibold">Órdenes</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase font-semibold">Pendientes</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase font-semibold">En proceso</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $stats['in_process'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase font-semibold">Pagadas</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['paid'] }}</p>
        </div>
    </div>

    <div class="orders-index-filters bg-white rounded-xl border border-gray-100 p-4 shadow-sm"
        :class="{ 'is-open': filtersOpen }">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="orders-filter-form">
            <div class="orders-filter-search relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}"
                    placeholder="Buscar por orden, cliente, correo, tipo o estado"
                    class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm">
            </div>

            <div class="orders-filter-secondary">
                <select name="work_status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                    <option value="">Estado operativo</option>
                    @foreach(\App\Models\Order::workStatusLabels() as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(($workStatus ?? '') === $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <select name="assigned_to" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                    <option value="">Responsable</option>
                    @foreach($workers as $worker)
                        <option value="{{ $worker->id }}" @selected(($assignedTo ?? '') == $worker->id)>{{ $worker->name }}</option>
                    @endforeach
                </select>
                <select name="date_filter" x-model="dateFilter"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm">
                    <option value="">Fecha</option>
                    <option value="today" @selected(($dateFilter ?? '') === 'today')>Hoy</option>
                    <option value="tomorrow" @selected(($dateFilter ?? '') === 'tomorrow')>Mañana</option>
                    <option value="week" @selected(($dateFilter ?? '') === 'week')>Esta semana</option>
                    <option value="range" @selected(($dateFilter ?? '') === 'range')>Rango personalizado</option>
                </select>
            </div>

            <div class="orders-filter-actions flex gap-2">
                <button class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition"
                    title="Buscar" aria-label="Buscar">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </button>
                @if($activeFilterCount > 0)
                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        title="Limpiar" aria-label="Limpiar">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </a>
                @endif
            </div>

            <div class="orders-filter-range" x-show="dateFilter === 'range'" x-cloak>
                <label>
                    <span>Desde</span>
                    <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm">
                </label>
                <label>
                    <span>Hasta</span>
                    <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-sm">
                </label>
            </div>
        </form>
    </div>
    @php
        $statusBadges = [
            'pending' => 'bg-yellow-100 text-yellow-700',
            'reserved' => 'bg-blue-100 text-blue-700',
            'paid' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-600',
        ];
        $statusLabels = [
            'pending' => 'Pendiente',
            'reserved' => 'Reservada',
            'paid' => 'Pagada',
            'failed' => 'Fallida',
            'cancelled' => 'Cancelada',
        ];
    @endphp

    <div class="orders-mobile-list md:hidden space-y-3">
        @forelse($orders as $order)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-mono text-xs text-gray-400">#{{ $order->id }}</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $order->user->name ?? 'Invitado' }}</p>
                        <p class="text-xs text-gray-400 break-words">{{ $order->user->email ?? '' }}</p>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Total</p>
                        <p class="font-semibold text-gray-800">${{ number_format($order->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Tipo</p>
                        <p class="text-gray-700">{{ ($order->order_type ?? 'purchase') === 'reservation' ? 'Reserva' : 'Compra' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs uppercase text-gray-400 font-semibold">Agenda</p>
                        <p class="text-gray-700">{{ $order->scheduled_at?->format('d/m/Y H:i') ?? $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        @if($order->scheduled_at)
                            <p class="text-xs text-gray-400">Creada: {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Trabajo</p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $order->work_status_badge }}">
                            {{ $order->work_status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-400 font-semibold">Responsable</p>
                        <p class="text-gray-700">{{ $order->assignedTo?->name ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs uppercase text-gray-400 font-semibold">Venta enlazada</p>
                        <p class="text-gray-700">{{ $order->sale_id ? '#' . $order->sale_id : 'Pendiente de venta comercial' }}</p>
                    </div>
                </div>

                @if($order->workTransitions())
                    <div class="flex flex-wrap gap-2">
                        @foreach($order->workTransitions() as $nextStatus => $nextLabel)
                            <form method="POST" action="{{ route('admin.orders.work-status', $order) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="work_status" value="{{ $nextStatus }}">
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition text-xs font-semibold">
                                    {{ $nextLabel }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition" title="Ver orden" aria-label="Ver orden">
                        <x-heroicon-o-eye class="w-5 h-5" />
                    </a>
                    @if(($order->order_type ?? 'purchase') === 'reservation' && $order->status !== 'paid')
                        <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition" title="Marcar pagada" aria-label="Marcar pagada">
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('¿Eliminar esta orden? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Eliminar orden" aria-label="Eliminar orden">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 px-4 py-8 text-center text-gray-400">No hay órdenes registradas</div>
        @endforelse
    </div>

    <div class="orders-table-shell hidden md:block bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="orders-table-scroll">
            <table class="w-full table-fixed text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center w-[7%]">#</th>
                        <th class="px-3 py-3 text-center w-[20%]">Cliente</th>
                        <th class="px-3 py-3 text-center w-[10%]">Total</th>
                        <th class="px-3 py-3 text-center w-[10%]">Tipo</th>
                        <th class="px-3 py-3 text-center w-[11%]">Pago</th>
                        <th class="px-3 py-3 text-center w-[13%]">Trabajo</th>
                        <th class="px-3 py-3 text-center w-[14%]">Agenda</th>
                        <th class="px-3 py-3 text-center w-[15%]">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-3 text-center text-gray-400 font-mono text-xs truncate">#{{ $order->id }}</td>
                            <td class="px-3 py-3 text-center">
                                <p class="font-medium text-gray-800 truncate">{{ $order->user->name ?? 'Invitado' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $order->user->email ?? '' }}</p>
                            </td>
                            <td class="px-3 py-3 text-center font-semibold text-gray-800 truncate">${{ number_format($order->total, 2) }}</td>
                            <td class="px-3 py-3 text-center">
                                @if(($order->order_type ?? 'purchase') === 'reservation')
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Reserva</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium">Compra</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusBadges[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $order->work_status_badge }}">
                                    {{ $order->work_status_label }}
                                </span>
                                <p class="text-[11px] text-gray-400 truncate mt-1">{{ $order->assignedTo?->name ?? 'Sin responsable' }}</p>
                                @if($order->sale_id)
                                    <p class="text-[11px] text-emerald-600 truncate mt-0.5">Venta #{{ $order->sale_id }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center text-gray-500 text-xs">
                                <p class="truncate">{{ $order->scheduled_at?->format('d/m/Y H:i') ?? $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                @if($order->scheduled_at)
                                    <p class="text-[11px] text-gray-400 truncate">Creada: {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 flex-wrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Ver orden" aria-label="Ver orden">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    @foreach($order->workTransitions() as $nextStatus => $nextLabel)
                                        <form method="POST" action="{{ route('admin.orders.work-status', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="work_status" value="{{ $nextStatus }}">
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition" title="{{ $nextLabel }}" aria-label="{{ $nextLabel }}">
                                                @if($nextStatus === \App\Models\Order::WORK_ARRIVED)
                                                    <x-heroicon-o-map-pin class="w-4 h-4" />
                                                @elseif($nextStatus === \App\Models\Order::WORK_IN_PROGRESS)
                                                    <x-heroicon-o-play class="w-4 h-4" />
                                                @elseif($nextStatus === \App\Models\Order::WORK_READY)
                                                    <x-heroicon-o-clipboard-document-check class="w-4 h-4" />
                                                @elseif($nextStatus === \App\Models\Order::WORK_COMPLETED)
                                                    <x-heroicon-o-check-circle class="w-4 h-4" />
                                                @else
                                                    <x-heroicon-o-x-circle class="w-4 h-4" />
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                    @if(($order->order_type ?? 'purchase') === 'reservation' && $order->status !== 'paid')
                                        <form method="POST" action="{{ route('admin.orders.marcar-pagada', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-green-600 hover:bg-green-50 transition" title="Marcar pagada" aria-label="Marcar pagada">
                                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('¿Eliminar esta orden? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition" title="Eliminar orden" aria-label="Eliminar orden">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">No hay órdenes registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
        <div class="orders-index-pagination">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
