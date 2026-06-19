<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class VentasController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Order::query()->with(['user', 'transaction', 'items']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $ventas = $query->latest()->paginate(15)->withQueryString();

        $baseKpiQuery = Order::query();
        if ($dateFrom) {
            $baseKpiQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $baseKpiQuery->whereDate('created_at', '<=', $dateTo);
        }

        $stats = [
            'total_ventas' => (float) (clone $baseKpiQuery)->where('status', 'paid')->sum('total'),
            'total_ordenes' => (int) (clone $baseKpiQuery)->count(),
            'ordenes_pagadas' => (int) (clone $baseKpiQuery)->where('status', 'paid')->count(),
            'ticket_promedio' => (float) ((clone $baseKpiQuery)->where('status', 'paid')->avg('total') ?? 0),
        ];

        return view('admin.ventas.index', compact('ventas', 'stats', 'search', 'status', 'dateFrom', 'dateTo'));
    }

    public function show(Order $venta)
    {
        $venta->load(['user', 'items.itemable', 'items.vehicle.brand', 'items.vehicle.model', 'items.vehicleType', 'transaction']);

        return view('admin.ventas.show', compact('venta'));
    }

    public function create()
    {
        $clientes = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.ventas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,paid,reserved,failed,cancelled'],
            'order_type' => ['required', 'in:purchase,reservation'],
        ]);

        $venta = Order::create($data);

        return redirect()->route('admin.ventas.show', $venta)->with('success', 'Venta creada correctamente.');
    }

    public function edit(Order $venta)
    {
        $clientes = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.ventas.edit', compact('venta', 'clientes'));
    }

    public function update(Request $request, Order $venta)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,paid,reserved,failed,cancelled'],
            'order_type' => ['required', 'in:purchase,reservation'],
        ]);

        $venta->update($data);

        return redirect()->route('admin.ventas.show', $venta)->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(Order $venta)
    {
        $venta->delete();

        return redirect()->route('admin.ventas.index')->with('success', 'Venta eliminada correctamente.');
    }
}
