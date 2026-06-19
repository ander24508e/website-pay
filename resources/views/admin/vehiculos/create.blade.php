@extends('layouts.admin')

@section('title', 'Nuevo Vehiculo')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.vehiculos.index') }}"
                    class="inline-flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800"
                    title="Volver" aria-label="Volver">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-800">Nuevo Vehiculo</h2>
                    <p class="text-gray-500 text-sm mt-1">Registra un vehiculo y enlazalo con un cliente.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.vehiculos.store') }}" class="space-y-6">
            @include('admin.vehiculos._form', ['buttonText' => 'Guardar vehiculo'])
        </form>
    </div>
@endsection
