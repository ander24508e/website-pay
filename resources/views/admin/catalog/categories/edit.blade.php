@extends('layouts.admin')

@section('title', 'Editar Categoria')

@section('content')
@php
    $returnToType = (bool) request()->boolean('return_to_type');
    $returnUrl = $returnToType
        ? route('admin.catalog-types.show', $catalogCategory->catalog_type_id)
        : route('admin.catalog-categories.index', ['catalog_type_id' => $catalogCategory->catalog_type_id]);
    $selectedTypeId = (int) old('catalog_type_id', $catalogCategory->catalog_type_id);
    $selectedType = $types->firstWhere('id', $selectedTypeId);
    $showTypeSelector = !($returnToType && $selectedType);
    $inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
    $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
@endphp

<div class="mx-auto w-full max-w-[1500px] px-3 pb-4 sm:px-5 xl:h-[calc(100vh-2rem)] xl:overflow-hidden">
    <div class="mb-3 flex items-center gap-3">
        <a href="{{ $returnUrl }}"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="min-w-0">
            <h2 class="text-xl font-bold leading-tight text-gray-900 sm:text-2xl">Editar Categoria</h2>
            <p class="truncate text-sm text-gray-400">Modificando <strong class="text-gray-600">{{ $catalogCategory->name }}</strong></p>
        </div>
    </div>

    <form action="{{ route('admin.catalog-categories.update', $catalogCategory) }}" method="POST"
        class="rounded-lg bg-white shadow-sm xl:flex xl:h-[calc(100%-4.25rem)] xl:flex-col xl:overflow-hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to_type" value="{{ $returnToType ? 1 : 0 }}">
        @if (old('active', $catalogCategory->active))
            <input type="hidden" name="active" value="1">
        @endif

        @include('admin.catalog.categories._form')

        <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
            <a href="{{ $returnUrl }}"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                Cancelar
            </a>
            <button type="submit"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                Actualizar Categoria
            </button>
        </div>
    </form>
</div>
@endsection

@include('admin.catalog.partials._slug-script', ['nameInputId' => 'category_name', 'slugInputId' => 'category_slug'])
