@extends('layouts.admin')

@section('title', 'Editar Vehículo')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.vehiculos.show', $vehiculo) }}"
                    class="inline-flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800"
                    title="Volver" aria-label="Volver">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-800">Editar Vehículo</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $vehiculo->plate }} · {{ $vehiculo->resolvedBrand()?->name }} {{ $vehiculo->resolvedModel()?->name }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.vehiculos.update', $vehiculo) }}" class="space-y-6">
            @method('PUT')
            @include('admin.vehiculos._form', ['buttonText' => 'Actualizar vehículo'])
        </form>
    </div>
@endsection
