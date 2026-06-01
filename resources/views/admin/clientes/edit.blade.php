@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Editar Cliente</h2>
        <a href="{{ route('admin.clientes.show', $cliente) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $cliente->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña (opcional)</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Actualizar Cliente</button>
    </form>
</div>
@endsection

