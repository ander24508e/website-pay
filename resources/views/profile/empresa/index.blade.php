@extends('layouts.admin')

@section('title', 'Mi Empresa')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">🏢 Mi Empresa</h2>
        <p class="text-gray-500 text-sm mt-1">Configura el nombre, logo e información de contacto</p>
    </div>
</div>

{{-- Alerta éxito --}}
@if(session('status') === 'empresa-updated')
    <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
        <span>✅ Información actualizada correctamente.</span>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 font-bold text-lg leading-none">×</button>
    </div>
@endif

<form method="POST" action="{{ route('admin.empresa.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna izquierda — Logo --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Logo de la Empresa</p>

                {{-- Preview --}}
                <div class="flex flex-col items-center mb-5">
                    <img src="{{ $empresa->logo_url }}"
                         id="logo-preview"
                         alt="Logo actual"
                         class="w-32 h-32 rounded-xl object-contain border border-gray-200 shadow-sm mb-2">
                    <span class="text-xs text-gray-400">Logo actual</span>
                </div>

                {{-- Upload --}}
                <input type="file" id="logo" name="logo" accept="image/*"
                       style="display:none;" onchange="previewLogo(this)">

                <button type="button"
                        onclick="document.getElementById('logo').click()"
                        class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm flex items-center justify-center gap-2">
                    ☁️ Cambiar Logo
                </button>

                <p id="file-name" class="text-xs text-gray-400 text-center mt-2">
                    {{ $empresa->logo ? basename($empresa->logo) : 'Ningún archivo seleccionado' }}
                </p>

                <p class="text-xs text-gray-400 text-center mt-1">JPG, PNG o SVG — Máx. 4MB</p>

                @error('logo')
                    <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                @enderror

                {{-- Eliminar logo --}}
                @if($empresa->logo)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <form action="{{ route('admin.empresa.deleteLogo') }}" method="POST"
                              onsubmit="return confirm('¿Eliminar el logo actual?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full text-red-500 hover:text-red-700 text-xs font-medium py-2 rounded-lg hover:bg-red-50 transition">
                                🗑 Eliminar logo actual
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Columna derecha — Información --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Información de la Empresa</p>

                {{-- Nombre --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa *</label>
                    <input type="text" name="nombre"
                           value="{{ old('nombre', $empresa->nombre) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm @error('nombre') border-red-400 @enderror"
                           placeholder="Ej: Lavadora y Lubricadora Endara">
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono"
                           value="{{ old('telefono', $empresa->telefono) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm"
                           placeholder="Ej: +593 99 999 9999">
                    @error('telefono')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dirección --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <textarea name="direccion" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm resize-none"
                              placeholder="Ej: Cayambe, Pichincha, Ecuador">{{ old('direccion', $empresa->direccion) }}</textarea>
                    @error('direccion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón guardar --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                       class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.size > 4 * 1024 * 1024) {
        alert('La imagen es muy grande. Máximo 4MB.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logo-preview').src = e.target.result;
    };
    reader.readAsDataURL(file);

    const nameEl = document.getElementById('file-name');
    nameEl.textContent = file.name;
    nameEl.classList.add('text-red-500');
    nameEl.classList.remove('text-gray-400');
}

// Auto-ocultar alerta
document.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.bg-green-100');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    }
});
</script>
@endpush