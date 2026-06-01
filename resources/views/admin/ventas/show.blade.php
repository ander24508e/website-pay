@extends('layouts.admin')

@section('title', 'Detalle Venta')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Venta #{{ $venta->id }}</h2>
            <p class="text-gray-500 text-sm">Detalle completo de orden y pago asociado.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ventas.edit', $venta) }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Editar</a>
            <form method="POST" action="{{ route('admin.ventas.destroy', $venta) }}" onsubmit="return confirm('¿Eliminar venta?');">
                @csrf
                @method('DELETE')
                <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium">Eliminar</button>
            </form>
            <a href="{{ route('admin.ventas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm lg:col-span-2">
            <h3 class="font-semibold text-gray-800 mb-3">Items de la venta</h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Item</th>
                            <th class="px-3 py-2 text-left">Tipo</th>
                            <th class="px-3 py-2 text-left">Cantidad</th>
                            <th class="px-3 py-2 text-left">Unitario</th>
                            <th class="px-3 py-2 text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->items as $item)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-700">{{ $item->item_display_name }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $item->item_type_label }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $item->quantity }}</td>
                                <td class="px-3 py-2 text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-3 py-2 font-semibold text-gray-700">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
            <h3 class="font-semibold text-gray-800">Resumen</h3>
            <div class="text-sm">
                <p class="text-gray-500">Cliente</p>
                <p class="font-medium text-gray-800">{{ $venta->user->name ?? 'Invitado' }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Estado</p>
                <p class="font-medium text-gray-800">{{ ucfirst($venta->status) }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Tipo</p>
                <p class="font-medium text-gray-800">{{ $venta->order_type ?? 'purchase' }}</p>
            </div>
            <div class="text-sm">
                <p class="text-gray-500">Fecha</p>
                <p class="font-medium text-gray-800">{{ $venta->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="pt-3 border-t border-gray-100">
                <p class="text-gray-500 text-sm">Total</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($venta->total, 2) }}</p>
            </div>
            @if($venta->transaction)
                <div class="pt-3 border-t border-gray-100 text-sm">
                    <p class="text-gray-500">Pago</p>
                    <p class="font-medium text-gray-800">{{ ucfirst($venta->transaction->status) }} - ${{ number_format($venta->transaction->amount, 2) }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
