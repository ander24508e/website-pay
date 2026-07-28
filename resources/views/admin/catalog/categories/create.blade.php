@extends('layouts.admin')

@section('title', 'Nueva Categoria')

@section('content')
@php
    $returnUrl = ($returnToType && $selectedTypeId > 0)
        ? route('admin.catalog-types.show', $selectedTypeId)
        : route('admin.catalog-categories.index');
    $selectedType = $selectedType ?? $types->firstWhere('id', $selectedTypeId);
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
            <h2 class="text-xl font-bold leading-tight text-gray-900 sm:text-2xl">Nueva Categoria</h2>
            <p class="text-sm text-gray-400">Crea una categoria para ordenar productos o servicios dentro del negocio.</p>
        </div>
    </div>

    @if ($types->isEmpty())
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Primero necesitas crear al menos una seccion para poder registrar categorias.
            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.catalog-types.create') }}"
                    class="inline-flex justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Crear seccion
                </a>
                <a href="{{ route('admin.catalog-categories.index') }}"
                    class="inline-flex justify-center rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                    Volver
                </a>
            </div>
        </div>
    @else
        <form action="{{ route('admin.catalog-categories.store') }}" method="POST"
            class="rounded-lg bg-white shadow-sm xl:flex xl:h-[calc(100%-4.25rem)] xl:flex-col xl:overflow-hidden">
            @csrf
            <input type="hidden" name="redirect_to_type" value="{{ $returnToType ? 1 : 0 }}">
            <input type="hidden" name="sort_order" value="{{ old('sort_order', 0) }}">
            <input type="hidden" name="active" value="1">
            @if ($returnToType && $selectedTypeId > 0)
                <input type="hidden" name="catalog_type_id" value="{{ $selectedTypeId }}">
            @endif

            <div class="grid gap-4 p-4 xl:overflow-y-auto">
                <div class="min-w-0 space-y-4">
                    <section class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Informacion principal</p>
                            <p class="text-xs text-gray-500">Nombre, slug y descripcion de la categoria.</p>
                        </div>

                        @unless ($returnToType && $selectedTypeId > 0)
                            <div>
                                <label class="{{ $labelClass }}">Seccion *</label>
                                <select name="catalog_type_id"
                                    class="{{ $inputClass }} @error('catalog_type_id') border-red-400 bg-red-50 @enderror">
                                    <option value="">Selecciona una seccion</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}" {{ old('catalog_type_id', $selectedTypeId ?: null) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('catalog_type_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endunless

                        <div>
                            <label class="{{ $labelClass }}">Nombre *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="{{ $inputClass }} @error('name') border-red-400 bg-red-50 @enderror"
                                placeholder="Categoria de tu negocio">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                class="{{ $inputClass }} @error('slug') border-red-400 bg-red-50 @enderror"
                                placeholder="Se genera automaticamente si lo dejas vacio">
                            @error('slug')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Descripcion</label>
                            <textarea name="description" rows="5"
                                class="{{ $inputClass }} resize-none @error('description') border-red-400 bg-red-50 @enderror"
                                placeholder="Describe que agrupa esta categoria">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>
                </div>
            </div>

            <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                <a href="{{ $returnUrl }}"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Guardar Categoria
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
