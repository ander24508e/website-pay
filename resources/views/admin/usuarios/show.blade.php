@extends('layouts.admin')

@section('title', 'Detalle Usuario')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-2xl font-bold text-gray-800 break-words">{{ $usuario->name }}</h2>
            <p class="text-gray-500 text-sm leading-relaxed">Detalle de usuario, rol y actividad comercial.</p>
        </div>
        <div class="flex flex-wrap gap-2 sm:flex-nowrap">
            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="inline-flex items-center justify-center bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Editar</a>
            @if(auth()->id() !== $usuario->id)
                <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" onsubmit="return confirm('¿Eliminar usuario?');">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex items-center justify-center bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-medium">Eliminar</button>
                </form>
            @endif
            <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center justify-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
            <h3 class="font-semibold text-gray-800">Perfil</h3>
            <div class="text-sm"><p class="text-gray-500">Correo</p><p class="font-medium text-gray-800 break-all">{{ $usuario->email }}</p></div>
            <div class="text-sm"><p class="text-gray-500">Telefono</p><p class="font-medium text-gray-800">{{ $usuario->telefono ?? '-' }}</p></div>
            <div class="text-sm"><p class="text-gray-500">Direccion</p><p class="font-medium text-gray-800">{{ $usuario->direccion ?? '-' }}</p></div>
            <div class="text-sm"><p class="text-gray-500">Rol actual</p><p class="font-medium text-gray-800">{{ ucfirst($usuario->roles->pluck('name')->first() ?? 'sin-rol') }}</p></div>
            <div class="text-sm"><p class="text-gray-500">Registro</p><p class="font-medium text-gray-800">{{ $resumen['registro']?->format('d/m/Y H:i') }}</p></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm xl:col-span-2">
            <h3 class="font-semibold text-gray-800 mb-3">Resumen Comercial</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500 uppercase font-semibold">Total Ordenes</p><p class="text-2xl font-bold text-gray-800">{{ $resumen['total_ordenes'] }}</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500 uppercase font-semibold">Total Pagado</p><p class="text-2xl font-bold text-gray-800">${{ number_format($resumen['total_comprado'], 2) }}</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-xs text-gray-500 uppercase font-semibold">Ultima Actividad</p><p class="text-sm font-semibold text-gray-800 mt-2">{{ $usuario->orders->first()?->created_at?->format('d/m/Y H:i') ?? 'Sin ordenes' }}</p></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Ultimas Ordenes</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[880px] text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Orden</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-right">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuario->orders as $order)
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'reserved' => 'bg-blue-100 text-blue-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-gray-100 text-gray-600',
                            ][$order->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3 font-semibold text-gray-700">#{{ $order->id }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="px-4 py-3 font-semibold text-gray-800">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.ventas.show', $order) }}" class="text-blue-600 hover:text-blue-700 font-medium">Ver venta</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin ordenes registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
