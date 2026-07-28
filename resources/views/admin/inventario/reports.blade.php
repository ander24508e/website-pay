@extends('layouts.admin')

@section('title', 'Reportes de Inventario')

@section('content')
<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Reportes de inventario</h2>
            <p class="text-sm text-gray-500 mt-1">Existencias, valorización, alertas, movimientos y utilidad básica.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.inventario.periods') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold">Cierres</a>
            <a href="{{ route('admin.inventario.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold">Volver</a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <select name="inventory_location_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todas las ubicaciones</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" {{ (int) request('inventory_location_id') === $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
            @endforeach
        </select>
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos los movimientos</option>
            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Entradas</option>
            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Salidas</option>
            <option value="adjust" {{ request('type') === 'adjust' ? 'selected' : '' }}>Ajustes</option>
        </select>
        <select name="reason" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Todos los motivos</option>
            @foreach($reasons as $reason)
                <option value="{{ $reason }}" {{ request('reason') === $reason ? 'selected' : '' }}>{{ $reason }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
        <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold">Filtrar</button>
    </form>

    <div class="grid grid-cols-2 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Unidades</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['units'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Valor inventario</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $canViewCosts ? '$' . number_format($stats['value'], 2) : 'Restringido' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Bajo stock</p>
            <p class="text-2xl font-bold text-amber-700 mt-2">{{ $stats['low'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Agotados</p>
            <p class="text-2xl font-bold text-red-700 mt-2">{{ $stats['out'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Sin costo</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['no_cost'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs uppercase text-gray-400 font-semibold">Utilidad</p>
            <p class="text-2xl font-bold text-green-700 mt-2">{{ $canViewCosts ? '$' . number_format($stats['profit'], 2) : 'Restringido' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Existencias valorizadas</h3>
                <a href="{{ route('admin.inventario.reports.export', ['section' => 'stock', ...request()->query()]) }}" class="text-sm font-semibold text-gray-700">Exportar</a>
            </div>
            <div class="overflow-x-auto max-h-[420px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Producto</th>
                            <th class="px-3 py-3 text-center">Stock</th>
                            <th class="px-3 py-3 text-center">Mín.</th>
                            <th class="px-3 py-3 text-center">Costo</th>
                            <th class="px-3 py-3 text-center">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($variants->take(80) as $variant)
                            <tr>
                                <td class="px-3 py-3 text-center">
                                    <p class="font-semibold text-gray-800">{{ $variant->item?->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $variant->name }} {{ $variant->sku ? '(' . $variant->sku . ')' : '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center">{{ $variant->stock ?? 0 }}</td>
                                <td class="px-3 py-3 text-center">{{ $variant->min_stock ?? 0 }}</td>
                                <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format((float) ($variant->cost_price ?? 0), 2) : '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format((int) ($variant->stock ?? 0) * (float) ($variant->cost_price ?? 0), 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Alertas</h3>
                <a href="{{ route('admin.inventario.reports.export', ['section' => 'alerts', ...request()->query()]) }}" class="text-sm font-semibold text-gray-700">Exportar</a>
            </div>
            <div class="divide-y max-h-[420px] overflow-y-auto">
                @forelse($alerts as $variant)
                    @php
                        $stock = (int) ($variant->stock ?? 0);
                        $minStock = (int) ($variant->min_stock ?? 0);
                    @endphp
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $variant->item?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $variant->name }} {{ $variant->sku ? '(' . $variant->sku . ')' : '' }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $stock <= 0 ? 'bg-red-100 text-red-700' : ((float) ($variant->cost_price ?? 0) <= 0 ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $stock <= 0 ? 'Agotado' : ((float) ($variant->cost_price ?? 0) <= 0 ? 'Sin costo' : 'Bajo stock') }}
                        </span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-400">Sin alertas activas.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Stock por ubicación</h3>
                <a href="{{ route('admin.inventario.reports.export', ['section' => 'locations', ...request()->query()]) }}" class="text-sm font-semibold text-gray-700">Exportar</a>
            </div>
            <div class="overflow-x-auto max-h-[420px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Ubicación</th>
                            <th class="px-3 py-3 text-center">Producto</th>
                            <th class="px-3 py-3 text-center">Cantidad</th>
                            <th class="px-3 py-3 text-center">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($locationStocks->take(120) as $stock)
                            <tr>
                                <td class="px-3 py-3 text-center">{{ $stock->location?->name }}</td>
                                <td class="px-3 py-3 text-center">{{ $stock->variant?->item?->name }}<br><span class="text-xs text-gray-400">{{ $stock->variant?->name }}</span></td>
                                <td class="px-3 py-3 text-center">{{ $stock->quantity }}</td>
                                <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format((int) $stock->quantity * (float) ($stock->variant?->cost_price ?? 0), 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Sin stock por ubicación.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Utilidad básica</h3>
                <a href="{{ route('admin.inventario.reports.export', ['section' => 'profit', ...request()->query()]) }}" class="text-sm font-semibold text-gray-700">Exportar</a>
            </div>
            <div class="overflow-x-auto max-h-[420px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Producto</th>
                            <th class="px-3 py-3 text-center">Ingreso</th>
                            <th class="px-3 py-3 text-center">Costo</th>
                            <th class="px-3 py-3 text-center">Utilidad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($profitRows->take(120) as $movement)
                            <tr>
                                <td class="px-3 py-3 text-center">{{ $movement->variant?->item?->name }}<br><span class="text-xs text-gray-400">{{ $movement->created_at?->format('d/m/Y') }}</span></td>
                                <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format($movement->profit_revenue, 2) : '-' }}</td>
                                <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format($movement->profit_cost, 2) : '-' }}</td>
                                <td class="px-3 py-3 text-center font-semibold {{ $movement->profit_amount >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ $canViewCosts ? '$' . number_format($movement->profit_amount, 2) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Sin ventas valorizadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Movimientos recientes</h3>
            <a href="{{ route('admin.inventario.reports.export', ['section' => 'movements', ...request()->query()]) }}" class="text-sm font-semibold text-gray-700">Exportar</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-3 text-center">Fecha</th>
                        <th class="px-3 py-3 text-center">Producto</th>
                        <th class="px-3 py-3 text-center">Tipo</th>
                        <th class="px-3 py-3 text-center">Cantidad</th>
                        <th class="px-3 py-3 text-center">Motivo</th>
                        <th class="px-3 py-3 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($movements->take(150) as $movement)
                        <tr>
                            <td class="px-3 py-3 text-center">{{ $movement->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-3 text-center">{{ $movement->variant?->item?->name }}<br><span class="text-xs text-gray-400">{{ $movement->variant?->name }}</span></td>
                            <td class="px-3 py-3 text-center font-semibold">{{ strtoupper($movement->type) }}</td>
                            <td class="px-3 py-3 text-center">{{ $movement->quantity }}</td>
                            <td class="px-3 py-3 text-center">{{ $movement->reason ?: '-' }}<br><span class="text-xs text-gray-400">{{ $movement->reference ?: '' }}</span></td>
                            <td class="px-3 py-3 text-center">{{ $canViewCosts ? '$' . number_format((float) ($movement->total_cost ?? 0), 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin movimientos con esos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
