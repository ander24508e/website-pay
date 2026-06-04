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
        <a href="{{ route('admin.clientes.create') }}"
            class="inline-flex items-center justify-center bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition"
            title="Nuevo cliente" aria-label="Nuevo cliente">
            <x-heroicon-o-plus class="w-5 h-5" />
        </a>
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
            <a href="{{ route('admin.clientes.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium text-center">Limpiar</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($clientes as $cliente)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h4 class="font-semibold text-gray-800 break-words">{{ $cliente->name }}</h4>
                            <p class="text-xs text-gray-400 mt-0.5">ID #{{ $cliente->id }}</p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 shrink-0">Cliente</span>
                    </div>

                    <div class="grid grid-cols-1 gap-2 mt-3 text-sm">
                        <div>
                            <p class="text-xs uppercase text-gray-400 font-semibold">Correo</p>
                            <p class="text-gray-700 break-words">{{ $cliente->email }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">Telefono</p>
                                <p class="text-gray-700 break-words">{{ $cliente->telefono ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">Ordenes</p>
                                <p class="text-gray-700 font-semibold">{{ $cliente->orders_count }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">Total</p>
                                <p class="text-gray-800 font-semibold">${{ number_format((float) ($cliente->total_compras ?? 0), 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase text-gray-400 font-semibold">Registro</p>
                                <p class="text-gray-700">{{ $cliente->created_at?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-4">
                        <a href="{{ route('admin.clientes.show', $cliente) }}"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                            title="Ver cliente" aria-label="Ver cliente">
                            <x-heroicon-o-eye class="w-5 h-5" />
                        </a>
                        <a href="{{ route('admin.clientes.edit', $cliente) }}"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                            title="Editar cliente" aria-label="Editar cliente">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                        </a>
                        <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Eliminar cliente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                title="Eliminar cliente" aria-label="Eliminar cliente">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-4 py-8 text-center text-gray-400">No hay clientes registrados.</div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-hidden">
            <table class="w-full table-fixed text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="w-[20%] px-4 py-3 text-center">Cliente</th>
                        <th class="w-[25%] px-4 py-3 text-center">Correo</th>
                        <th class="w-[13%] px-4 py-3 text-center">Telefono</th>
                        <th class="w-[8%] px-4 py-3 text-center">Ord.</th>
                        <th class="w-[13%] px-4 py-3 text-center">Total</th>
                        <th class="w-[10%] px-4 py-3 text-center">Registro</th>
                        <th class="w-[11%] px-4 py-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        <tr class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-center">
                                <div class="font-semibold text-gray-800 truncate">{{ $cliente->name }}</div>
                                <div class="text-xs text-gray-400">ID #{{ $cliente->id }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-center truncate">{{ $cliente->email }}</td>
                            <td class="px-4 py-3 text-gray-600 text-center truncate">{{ $cliente->telefono ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 text-center">{{ $cliente->orders_count }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 text-center">${{ number_format((float) ($cliente->total_compras ?? 0), 2) }}</td>
                            <td class="px-4 py-3 text-gray-500 text-center">{{ $cliente->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.clientes.show', $cliente) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                        title="Ver cliente" aria-label="Ver cliente">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.clientes.edit', $cliente) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                        title="Editar cliente" aria-label="Editar cliente">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Eliminar cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                            title="Eliminar cliente" aria-label="Eliminar cliente">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
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
