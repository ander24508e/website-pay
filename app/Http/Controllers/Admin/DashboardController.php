<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $startMonth = $now->copy()->startOfMonth();
        $endMonth = $now->copy()->endOfMonth();

        $kpis = [
            'ventas_mes' => (float) Order::query()
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startMonth, $endMonth])
                ->sum('total'),
            'ordenes_mes' => (int) Order::query()
                ->whereBetween('created_at', [$startMonth, $endMonth])
                ->count(),
            'clientes_nuevos_mes' => (int) User::query()
                ->whereBetween('created_at', [$startMonth, $endMonth])
                ->count(),
            'ingresos_cobrados_mes' => (float) Transaction::query()
                ->where('status', 'approved')
                ->whereBetween('created_at', [$startMonth, $endMonth])
                ->sum('amount'),
        ];

        $days = collect(range(6, 0))->map(function ($offset) {
            return now()->subDays($offset)->startOfDay();
        });

        $ordersByDayRaw = Order::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$days->first(), $days->last()->copy()->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $salesByDayRaw = Order::query()
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(total),0) as total')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$days->first(), $days->last()->copy()->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartDays = $days->map(fn ($d) => $d->format('d/m'));
        $chartOrders = $days->map(fn ($d) => (int) ($ordersByDayRaw[$d->toDateString()] ?? 0));
        $chartSales = $days->map(fn ($d) => round((float) ($salesByDayRaw[$d->toDateString()] ?? 0), 2));

        $statusRows = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabelsMap = [
            'pending' => 'Pendiente',
            'paid' => 'Pagada',
            'failed' => 'Fallida',
            'cancelled' => 'Cancelada',
            'reserved' => 'Reservada',
        ];

        $statusOrder = ['pending', 'paid', 'reserved', 'failed', 'cancelled'];
        $chartStatusLabels = collect($statusOrder)
            ->filter(fn ($status) => isset($statusRows[$status]))
            ->map(fn ($status) => $statusLabelsMap[$status] ?? ucfirst($status))
            ->values();

        $chartStatusData = collect($statusOrder)
            ->filter(fn ($status) => isset($statusRows[$status]))
            ->map(fn ($status) => (int) $statusRows[$status])
            ->values();

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->take(7)
            ->get();

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'chartDays' => $chartDays,
            'chartOrders' => $chartOrders,
            'chartSales' => $chartSales,
            'chartStatusLabels' => $chartStatusLabels,
            'chartStatusData' => $chartStatusData,
            'recentOrders' => $recentOrders,
        ]);
    }
}
