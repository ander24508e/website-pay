@extends('layouts.admin')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Nuevo Cliente</h2>
        <a href="{{ route('admin.clientes.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.clientes.store') }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <div class="relative">
                <input type="password" name="password" data-password-field
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-11 text-sm" required>
                <button type="button" data-toggle-password
                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 transition hover:text-gray-700"
                    aria-label="Ver contraseña">
                    <x-heroicon-o-eye class="h-5 w-5" data-eye-open />
                    <x-heroicon-o-eye-slash class="hidden h-5 w-5" data-eye-closed />
                </button>
            </div>
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Guardar Cliente</button>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const wrapper = button.closest('.relative');
                    const input = wrapper?.querySelector('[data-password-field]');
                    const eyeOpen = button.querySelector('[data-eye-open]');
                    const eyeClosed = button.querySelector('[data-eye-closed]');

                    if (!input) {
                        return;
                    }

                    const shouldShow = input.type === 'password';
                    input.type = shouldShow ? 'text' : 'password';
                    button.setAttribute('aria-label', shouldShow ? 'Ocultar contraseña' : 'Ver contraseña');
                    eyeOpen?.classList.toggle('hidden', shouldShow);
                    eyeClosed?.classList.toggle('hidden', !shouldShow);
                });
            });
        });
    </script>
@endpush
