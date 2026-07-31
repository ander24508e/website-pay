@extends('layouts.admin')

@section('title', 'Editar Presentacion')

@section('content')
@php
    $selectedItemId = (int) old('catalog_item_id', $catalogVariant->catalog_item_id);
    $showEmptyItemOption = false;
    $redirectToItem = $redirectToItem ?? request()->boolean('redirect_to_item');
    $returnUrl = $redirectToItem
        ? route('admin.catalog-items.show', $catalogVariant->catalog_item_id)
        : route('admin.catalog-variants.index');
@endphp

<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ $returnUrl }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Editar Presentacion</h2>
            <p class="text-gray-400 text-sm">Modificando <strong class="text-gray-600">{{ $catalogVariant->name }}</strong></p>
        </div>
    </div>

    <div class="mx-auto w-full max-w-6xl">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <form action="{{ route('admin.catalog-variants.update', $catalogVariant) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_to_item" value="{{ $redirectToItem ? 1 : 0 }}">

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
        </div>
    </div>
</div>
@endsection
