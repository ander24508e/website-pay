@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="mb-8">
    <div class="flex items-center gap-2">
        <x-heroicon-o-chart-bar class="w-8 h-8 text-gray-800" />
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    </div>
    <p class="text-gray-500 text-sm mt-1">Resumen general del catalogo universal y tu negocio</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Subnegocios</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\CatalogType::count() }}</p>
        <a href="{{ route('admin.catalog-types.index') }}" class="text-blue-500 text-xs mt-2 inline-block hover:underline flex items-center gap-1">
            Ver todos <x-heroicon-o-arrow-right class="w-3 h-3" />
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Items Universales</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\CatalogItem::count() }}</p>
        <a href="{{ route('admin.catalog-items.index') }}" class="text-green-500 text-xs mt-2 inline-block hover:underline flex items-center gap-1">
            Ver todos <x-heroicon-o-arrow-right class="w-3 h-3" />
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ã“rdenes</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Order::count() }}</p>
        <a href="{{ route('admin.orders.index') }}" class="text-yellow-500 text-xs mt-2 inline-block hover:underline flex items-center gap-1">
            Ver todas <x-heroicon-o-arrow-right class="w-3 h-3" />
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Transacciones</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Transaction::count() }}</p>
        <a href="{{ route('admin.transactions.index') }}" class="text-red-500 text-xs mt-2 inline-block hover:underline flex items-center gap-1">
            Ver todas <x-heroicon-o-arrow-right class="w-3 h-3" />
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="w-5 h-5 text-gray-600" />
                <h3 class="font-semibold text-gray-800">Ãšltimas Ã“rdenes</h3>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-400 hover:text-gray-700 flex items-center gap-1">
                Ver todas <x-heroicon-o-arrow-right class="w-3 h-3" />
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">#</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Cliente</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Total</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse(\App\Models\Order::with('user')->latest()->take(5)->get() as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-400">#{{ $order->id }}</td>
                    <td class="px-6 py-3 text-gray-700">{{ $order->user->name ?? 'Invitado' }}</td>
                    <td class="px-6 py-3 font-semibold">${{ number_format($order->total, 2) }}</td>
                    <td class="px-6 py-3">
                        @php
                            $colors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                                'reserved' => 'bg-blue-100 text-blue-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }} flex items-center w-fit">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No hay Ã³rdenes aÃºn.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <x-heroicon-o-bolt class="w-5 h-5 text-gray-600" />
            <h3 class="font-semibold text-gray-800">Accesos RÃ¡pidos</h3>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.catalog-types.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <x-heroicon-o-squares-plus class="w-8 h-8 text-gray-600 mb-1" />
                <span class="text-xs font-semibold text-gray-700">Nuevo Subnegocio</span>
            </a>
            <a href="{{ route('admin.catalog-categories.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <x-heroicon-o-tag class="w-8 h-8 text-gray-600 mb-1" />
                <span class="text-xs font-semibold text-gray-700">Nueva Categoria</span>
            </a>
            <a href="{{ route('admin.catalog-items.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <x-heroicon-o-archive-box class="w-8 h-8 text-gray-600 mb-1" />
                <span class="text-xs font-semibold text-gray-700">Nuevo Item</span>
            </a>
            <a href="{{ route('admin.empresa.edit') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <x-heroicon-o-building-office class="w-8 h-8 text-gray-600 mb-1" />
                <span class="text-xs font-semibold text-gray-700">Mi Empresa</span>
            </a>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 mb-1">
                <x-heroicon-o-currency-dollar class="w-4 h-4 text-gray-500" />
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ingresos este mes</p>
            </div>
            <p class="text-3xl font-bold text-gray-800">
                ${{ number_format(\App\Models\Transaction::where('status', 'approved')->whereMonth('created_at', now()->month)->sum('amount'), 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Solo transacciones aprobadas</p>
        </div>
    </div>
</div>

@endsection
