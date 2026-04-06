@extends('layouts.admin')

@section('title', 'Detalle Servicio')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.services.index') }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            ←
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">🛠️ {{ $service->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle del servicio</p>
        </div>
    </div>

    {{-- LAYOUT DOS COLUMNAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Columna izquierda --}}
        <div class="lg:col-span-1 flex flex-col gap-6">

            {{-- Card imagen --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen Referencial</p>
                <div class="flex flex-col items-center">
                    @if($service->image)
                        <img src="{{ Storage::url($service->image) }}"
                             alt="{{ $service->name }}"
                             class="w-40 h-40 rounded-xl object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center text-5xl border-2 border-dashed border-gray-200">
                            🛠️
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Sin imagen</p>
                    @endif
                </div>
            </div>

            {{-- Card estado --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Estado</p>
                <div class="flex items-center gap-3">
                    @if($service->active)
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Activo</p>
                            <p class="text-xs text-gray-400">Visible en el catálogo</p>
                        </div>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Inactivo</p>
                            <p class="text-xs text-gray-400">Oculto del catálogo</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card acciones --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Acciones</p>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('admin.services.edit', $service) }}"
                       class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm text-center">
                        ✏️ Editar Servicio
                    </a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este servicio definitivamente?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full bg-red-50 text-red-600 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                            🗑 Eliminar
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Columna derecha --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Card info principal --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Información del Servicio</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Nombre</p>
                        <p class="font-semibold text-gray-800">{{ $service->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Categoría</p>
                        @if($service->category)
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">
                                {{ $service->category->name }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Precio</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($service->price, 2) }}
                            <span class="text-sm text-gray-400 font-normal">/ servicio</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card descripción --}}
            @if($service->description)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Descripción</p>
                <p class="text-gray-700 leading-relaxed text-sm">{{ $service->description }}</p>
            </div>
            @endif

            {{-- Card metadatos --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Información del registro</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Creado</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $service->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Última actualización</p>
                        <p class="text-sm text-gray-700 font-medium">{{ $service->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection