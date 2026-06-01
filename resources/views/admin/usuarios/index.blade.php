@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <div class="flex items-center gap-2">
            <x-heroicon-o-user-group class="w-8 h-8 text-gray-800" />
            <h2 class="text-2xl font-bold text-gray-800">Usuarios / Empleados</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Gestion de usuarios del sistema, roles y contexto comercial.</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold">+ Nuevo Usuario</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><p class="text-xs text-gray-400 uppercase font-semibold">Total Usuarios</p><p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_usuarios'] }}</p></div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><p class="text-xs text-gray-400 uppercase font-semibold">Admins</p><p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['admins'] }}</p></div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><p class="text-xs text-gray-400 uppercase font-semibold">Empleados</p><p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['empleados'] }}</p></div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><p class="text-xs text-gray-400 uppercase font-semibold">Clientes</p><p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['clientes'] }}</p></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre, correo o telefono" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium">Buscar</button>
            <a href="{{ route('admin.usuarios.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Limpiar</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Telefono</th>
                        <th class="px-4 py-3 text-left">Rol</th>
                        <th class="px-4 py-3 text-left">Ordenes</th>
                        <th class="px-4 py-3 text-left">Registro</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        @php
                            $roleName = $usuario->roles->pluck('name')->first() ?? 'sin-rol';
                            $roleClass = [
                                'admin' => 'bg-red-100 text-red-700',
                                'empleado' => 'bg-blue-100 text-blue-700',
                                'cliente' => 'bg-emerald-100 text-emerald-700',
                            ][$roleName] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $usuario->name }}</div>
                                <div class="text-xs text-gray-400">ID #{{ $usuario->id }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $usuario->telefono ?? '-' }}</td>
                            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $roleClass }}">{{ ucfirst($roleName) }}</span></td>
                            <td class="px-4 py-3 text-gray-700">{{ $usuario->orders_count }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.usuarios.show', $usuario) }}" class="text-blue-600 hover:text-blue-700 font-medium mr-3">Ver</a>
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-gray-600 hover:text-gray-800 font-medium mr-3">Editar</a>
                                <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="inline" onsubmit="return confirm('¿Eliminar usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $usuarios->links() }}</div>
</div>
@endsection
