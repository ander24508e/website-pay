@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <div class="flex items-center gap-2">
            <x-heroicon-o-chart-bar class="w-8 h-8 text-gray-800" />
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
        </div>
        <p class="text-gray-500 text-sm mt-1">Resumen ejecutivo de ventas, clientes y ordenes del negocio.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ventas del Mes</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">${{ number_format($kpis['ventas_mes'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-2">Solo ordenes pagadas</p>
        </article>

        <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ordenes del Mes</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $kpis['ordenes_mes'] }}</p>
            <a href="{{ route('admin.ventas.index') }}" class="text-blue-600 text-xs mt-2 inline-flex items-center gap-1 hover:underline">
                Ver ventas <x-heroicon-o-arrow-right class="w-3 h-3" />
            </a>
        </article>

        <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Clientes Nuevos</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $kpis['clientes_nuevos_mes'] }}</p>
            <p class="text-xs text-gray-400 mt-2">Registrados este mes</p>
        </article>

        <article class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Ingresos Cobrados</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">${{ number_format($kpis['ingresos_cobrados_mes'], 2) }}</p>
            <a href="{{ route('admin.transactions.index') }}" class="text-emerald-600 text-xs mt-2 inline-flex items-center gap-1 hover:underline">
                Ver transacciones <x-heroicon-o-arrow-right class="w-3 h-3" />
            </a>
        </article>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-gray-800">Ventas y Ordenes (ultimos 7 dias)</h3>
                    <p class="text-xs text-gray-400">Comparativa diaria para decisiones rapidas</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="ventasChart"></canvas>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800">Ordenes por Estado</h3>
            <p class="text-xs text-gray-400 mb-4">Distribucion general del flujo de ordenes</p>
            <div class="h-72">
                <canvas id="estadoChart"></canvas>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-shopping-bag class="w-5 h-5 text-gray-600" />
                    <h3 class="font-semibold text-gray-800">Ultimas Ordenes</h3>
                </div>
                <a href="{{ route('admin.ventas.index') }}" class="text-xs text-gray-400 hover:text-gray-700 flex items-center gap-1">
                    Ver ventas <x-heroicon-o-arrow-right class="w-3 h-3" />
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="text-left px-6 py-3">#</th>
                            <th class="text-left px-6 py-3">Cliente</th>
                            <th class="text-left px-6 py-3">Total</th>
                            <th class="text-left px-6 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'failed' => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                    'reserved' => 'bg-blue-100 text-blue-700',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-400">#{{ $order->id }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $order->user->name ?? 'Invitado' }}</td>
                                <td class="px-6 py-3 font-semibold">${{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No existen ordenes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-bolt class="w-5 h-5 text-gray-600" />
                <h3 class="font-semibold text-gray-800">Accesos Rapidos</h3>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.ventas.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                    <x-heroicon-o-banknotes class="w-7 h-7 text-gray-600 mb-1" />
                    <span class="text-xs font-semibold text-gray-700">Ver Ventas</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                    <x-heroicon-o-shopping-bag class="w-7 h-7 text-gray-600 mb-1" />
                    <span class="text-xs font-semibold text-gray-700">Ordenes</span>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                    <x-heroicon-o-credit-card class="w-7 h-7 text-gray-600 mb-1" />
                    <span class="text-xs font-semibold text-gray-700">Transacciones</span>
                </a>
                <a href="{{ route('admin.empresa.edit') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition text-center">
                    <x-heroicon-o-building-office class="w-7 h-7 text-gray-600 mb-1" />
                    <span class="text-xs font-semibold text-gray-700">Mi Empresa</span>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($chartDays);
const ordersData = @json($chartOrders);
const salesData = @json($chartSales);
const statusLabels = @json($chartStatusLabels);
const statusData = @json($chartStatusData);

const ventasCtx = document.getElementById('ventasChart');
if (ventasCtx) {
    new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Ordenes',
                    data: ordersData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'y',
                },
                {
                    label: 'Ventas ($)',
                    data: salesData,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.12)',
                    tension: 0.35,
                    fill: true,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Ordenes' }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Ventas ($)' }
                }
            }
        }
    });
}

const estadoCtx = document.getElementById('estadoChart');
if (estadoCtx) {
    new Chart(estadoCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#9ca3af'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endpush
