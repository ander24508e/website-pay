@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <div class="flex items-center gap-2">
            <x-heroicon-o-users class="w-8 h-8 text-gray-800" />
            <h2 class="text-2xl font-bold text-gray-800">Clientes</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Listado de clientes registrados con historial de compras.</p>
        </div>
        <a href="{{ route('admin.clientes.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold">+ Nuevo Cliente</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Clientes</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_clientes'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Nuevos Este Mes</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['nuevos_mes'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Con Compras</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['clientes_con_compras'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Ingreso Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($stats['ingreso_total_clientes'], 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre, correo o telefono" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Buscar</button>
            <a href="{{ route('admin.clientes.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Limpiar</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Telefono</th>
                        <th class="px-4 py-3 text-left">Ordenes</th>
                        <th class="px-4 py-3 text-left">Total Comprado</th>
                        <th class="px-4 py-3 text-left">Registro</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $cliente->name }}</div>
                                <div class="text-xs text-gray-400">ID #{{ $cliente->id }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $cliente->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $cliente->telefono ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cliente->orders_count }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">${{ number_format((float) ($cliente->total_compras ?? 0), 2) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $cliente->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.clientes.show', $cliente) }}" class="text-blue-600 hover:text-blue-700 font-medium mr-3">Ver</a>
                                <a href="{{ route('admin.clientes.edit', $cliente) }}" class="text-gray-700 hover:text-gray-900 font-medium mr-3">Editar</a>
                                <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" class="inline" onsubmit="return confirm('¿Eliminar cliente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $clientes->links() }}
    </div>
</div>
@endsection
