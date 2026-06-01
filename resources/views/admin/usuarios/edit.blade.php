@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Usuario</h2>
            <p class="text-gray-500 text-sm">Actualiza datos y asigna el rol del usuario.</p>
        </div>
        <a href="{{ route('admin.usuarios.show', $usuario) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Cancelar</a>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @error('telefono')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    @php $currentRole = old('role', $usuario->roles->pluck('name')->first()); @endphp
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected($currentRole === $role->name)>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
            <textarea name="direccion" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('direccion', $usuario->direccion) }}</textarea>
            @error('direccion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm font-medium">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
