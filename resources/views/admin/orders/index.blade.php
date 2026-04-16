@extends('layouts.admin')

@section('title', 'Órdenes')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Órdenes</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Historial de todas las órdenes realizadas</p>
        </div>
        {{-- Contador rápido --}}
        <div class="flex gap-3">
            <span class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <x-heroicon-o-clock class="w-3 h-3" />
                Pendientes: {{ $orders->where('status', 'pending')->count() }}
            </span>
            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1">
                <x-heroicon-o-check-circle class="w-3 h-3" />
                Pagadas: {{ $orders->where('status', 'paid')->count() }}
            </span>
        </div>
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[700px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">#</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Cliente</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Total</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Fecha</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">

                            {{-- ID --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-400 font-mono text-xs">
                                #{{ $order->id }}
                            </td>

                            {{-- Cliente --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $order->user->name ?? 'Invitado' }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->user->email ?? '—' }}</p>
                                </div>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                ${{ number_format($order->total, 2) }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @php
                                    $badges = [
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'paid'      => 'bg-green-100 text-green-700',
                                        'failed'    => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                    ];
                                    $icons = [
                                        'pending'   => '<x-heroicon-o-clock class="w-3 h-3 inline mr-1" />',
                                        'paid'      => '<x-heroicon-o-check-circle class="w-3 h-3 inline mr-1" />',
                                        'failed'    => '<x-heroicon-o-x-circle class="w-3 h-3 inline mr-1" />',
                                        'cancelled' => '<x-heroicon-o-ban class="w-3 h-3 inline mr-1" />',
                                    ];
                                    $labels = [
                                        'pending'   => 'Pendiente',
                                        'paid'      => 'Pagada',
                                        'failed'    => 'Fallida',
                                        'cancelled' => 'Cancelada',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $badges[$order->status] ?? 'bg-gray-100 text-gray-600' }} flex items-center gap-1 w-fit">
                                    {!! $icons[$order->status] ?? '<x-heroicon-o-question-mark-circle class="w-3 h-3 inline mr-1" />' !!}
                                    {{ $labels[$order->status] ?? $order->status }}
                                </span>
                            </td>

                            {{-- Fecha --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 text-xs">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Acciones --}}
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1 flex items-center gap-1">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                    Ver detalle
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <x-heroicon-o-shopping-bag class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                No hay órdenes registradas aún
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        @if($orders->hasPages())
            <div class="p-4 border-t">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

</div>
@endsection