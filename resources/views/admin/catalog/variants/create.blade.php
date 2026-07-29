@extends('layouts.admin')

@section('title', 'Nueva Presentacion')

@section('content')
@php
    $catalogVariant = null;
    $showEmptyItemOption = !($selectedItemId > 0 || $selectedTypeId > 0);
    $returnUrl = ($returnToType && $selectedTypeId > 0)
        ? route('admin.catalog-types.show', $selectedTypeId)
        : route('admin.catalog-variants.index');
@endphp

<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ $returnUrl }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Nueva Presentacion</h2>
            <p class="text-gray-400 text-sm">Agrega un tamaño, version o precio diferente a un producto.</p>
        </div>
    </div>

    <div class="mx-auto w-full max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            @if($items->isEmpty())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    Primero necesitas crear al menos un producto para poder registrar presentaciones.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.catalog-items.create') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm inline-block">
                        Crear Producto
                    </a>
                </div>
            @else
                <form action="{{ route('admin.catalog-variants.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="redirect_to_type" value="{{ $returnToType ? 1 : 0 }}">

                    @include('admin.catalog.variants._form')

                    <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                        <button type="submit"
                                class="w-full bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm sm:w-auto">
                            Guardar Presentacion
                        </button>
                        <a href="{{ $returnUrl }}"
                           class="w-full bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center sm:w-auto">
                            Cancelar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
