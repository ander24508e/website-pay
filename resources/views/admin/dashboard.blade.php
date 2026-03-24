@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">📊 Dashboard</h2>
    <p class="text-gray-500 text-sm mt-1">Resumen general de tu negocio</p>
</div>

{{-- Tarjetas de resumen --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Productos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Product::count() }}</p>
        <a href="{{ route('admin.products.index') }}" class="text-blue-500 text-xs mt-2 inline-block hover:underline">Ver todos →</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Servicios</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Service::count() }}</p>
        <a href="{{ route('admin.services.index') }}" class="text-green-500 text-xs mt-2 inline-block hover:underline">Ver todos →</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Órdenes</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Order::count() }}</p>
        <a href="{{ route('admin.orders.index') }}" class="text-yellow-500 text-xs mt-2 inline-block hover:underline">Ver todas →</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Transacciones</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ \App\Models\Transaction::count() }}</p>
        <a href="{{ route('admin.transactions.index') }}" class="text-red-500 text-xs mt-2 inline-block hover:underline">Ver todas →</a>
    </div>

</div>

{{-- Segunda fila --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Últimas órdenes --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">🧾 Últimas Órdenes</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Ver todas</a>
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
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'paid'      => 'bg-green-100 text-green-700',
                                'failed'    => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No hay órdenes aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Accesos rápidos --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">⚡ Accesos Rápidos</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.products.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <span class="text-2xl mb-1">📦</span>
                <span class="text-xs font-semibold text-gray-700">Nuevo Producto</span>
            </a>
            <a href="{{ route('admin.services.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <span class="text-2xl mb-1">🛠️</span>
                <span class="text-xs font-semibold text-gray-700">Nuevo Servicio</span>
            </a>
            <a href="{{ route('admin.categories.create') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <span class="text-2xl mb-1">🏷️</span>
                <span class="text-xs font-semibold text-gray-700">Nueva Categoría</span>
            </a>
            <a href="{{ route('admin.empresa.edit') }}"
               class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                <span class="text-2xl mb-1">🏢</span>
                <span class="text-xs font-semibold text-gray-700">Mi Empresa</span>
            </a>
        </div>

        {{-- Ingresos del mes --}}
        <div class="mt-6 pt-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Ingresos este mes</p>
            <p class="text-3xl font-bold text-gray-800">
                ${{ number_format(\App\Models\Transaction::where('status', 'approved')->whereMonth('created_at', now()->month)->sum('amount'), 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Solo transacciones aprobadas</p>
        </div>
    </div>

</div>

@endsection