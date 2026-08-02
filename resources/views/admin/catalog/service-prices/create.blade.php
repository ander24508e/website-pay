@extends('layouts.admin')

@section('title', 'Nuevo Precio por Vehiculo')

@section('content')
<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ $returnUrl }}"
           class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Nuevo Precio por Vehiculo</h2>
            <p class="text-sm text-gray-400">Agrega un precio para {{ $service->name }} segun el tipo de vehiculo.</p>
        </div>
    </div>

    <div class="mx-auto w-full max-w-6xl">
        <div class="rounded-xl bg-white p-4 shadow-sm sm:p-8">
            <form action="{{ route('admin.catalog-service-prices.store', $service) }}" method="POST">
                @csrf

                @include('admin.catalog.service-prices._form')

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <button type="submit" class="w-full rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700 sm:w-auto">
                        Guardar Precio
                    </button>
                    <a href="{{ $returnUrl }}" class="w-full rounded-lg bg-gray-100 px-6 py-2.5 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-200 sm:w-auto">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
