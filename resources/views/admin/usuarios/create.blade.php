@extends('layouts.admin')

@section('title', 'Nuevo trabajador')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Nuevo trabajador</h2>
            <p class="text-sm text-gray-500">Crea una cuenta interna. Los clientes se registran exclusivamente desde el sitio web.</p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-6">
        @csrf
        <section class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-800">Datos de acceso</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label><input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>@error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Correo</label><input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>@error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label><input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label><input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Rol interno</label><select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>@foreach($roles as $role)<option value="{{ $role->name }}" @selected(old('role', 'empleado') === $role->name)>{{ $role->name === 'gerente' ? 'Gerente' : 'Empleado' }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Gerente responsable</label><select name="manager_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">Sin asignar</option>@foreach($managers as $manager)<option value="{{ $manager->id }}" @selected((string) old('manager_id') === (string) $manager->id)>{{ $manager->name }}</option>@endforeach</select></div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" class="w-full border border-gray-300 rounded-lg pl-3 pr-11 py-2 text-sm" required>
                        <button type="button" data-password-toggle="password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-400 rounded-r-lg" aria-label="Mostrar contraseña" aria-pressed="false">
                            <x-heroicon-o-eye class="h-5 w-5" data-password-visible-icon />
                            <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-password-hidden-icon />
                        </button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg pl-3 pr-11 py-2 text-sm" required>
                        <button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-400 rounded-r-lg" aria-label="Mostrar confirmación de contraseña" aria-pressed="false">
                            <x-heroicon-o-eye class="h-5 w-5" data-password-visible-icon />
                            <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-password-hidden-icon />
                        </button>
                    </div>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="hidden" name="active" value="0"><input type="checkbox" name="active" value="1" @checked(old('active', '1') === '1') class="rounded border-gray-300"> Permitir acceso inmediatamente</label>
        </section>

        @if($canAssignPermissions)
            <section class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
                <div><h3 class="font-semibold text-gray-800">Permisos individuales</h3><p class="text-sm text-gray-500">Selecciona solamente las tareas que podrá ejecutar.</p></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($permissionGroups as $group)
                        <fieldset class="border border-gray-200 rounded-lg p-4"><legend class="px-2 text-sm font-semibold text-gray-700">{{ $group['label'] }}</legend><div class="space-y-2 mt-1">@foreach($group['permissions'] as $permission)<label class="flex items-start gap-2 text-sm text-gray-600"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', []), true)) class="mt-0.5 rounded border-gray-300"><span>{{ str($permission->name)->after('.')->replace('_', ' ')->headline() }}</span></label>@endforeach</div></fieldset>
                    @endforeach
                </div>
            </section>
        @endif

        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Crear trabajador</button>
    </form>
</div>
@endsection
