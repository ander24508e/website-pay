@extends('layouts.admin')

@section('title', 'Catalogo')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="mb-6">
        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-3">
            <span>Admin</span>
            <span>/</span>
            <span class="text-gray-600 font-semibold">Catalogo</span>
        </div>
        <div class="flex items-center gap-2">
            <x-heroicon-o-squares-2x2 class="w-8 h-8 text-gray-800" />
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catalogo</h2>
        </div>
        <p class="text-gray-500 text-sm mt-1">Administrar Catálogo.</p>
    </div>

    {{-- <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.catalog-types.create') }}" class="bg-gray-900 text-white px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-gray-700 transition">
            + Nuevo Subnegocio
        </a>
        <a href="{{ route('admin.catalog-categories.create') }}" class="bg-white text-gray-700 px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-gray-50 transition border border-gray-200">
            + Nueva Categoria
        </a>
        <a href="{{ route('admin.catalog-items.create') }}" class="bg-white text-gray-700 px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-gray-50 transition border border-gray-200">
            + Nuevo Item
        </a>
        <a href="{{ route('admin.catalog-variants.create') }}" class="bg-white text-gray-700 px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-gray-50 transition border border-gray-200">
            + Nueva Variante
        </a>
    </div> --}}

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="{{ route('admin.catalog-types.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-squares-plus class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Subnegocios</h3>
            </div>
            <p class="text-sm text-gray-500">Crea bloques como restaurante, bar, taller, industrial o cualquier otra linea del negocio.</p>
        </a>

        <a href="{{ route('admin.catalog-categories.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-tag class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Categorias Universales</h3>
            </div>
            <p class="text-sm text-gray-500">Organiza los items dentro de cada subnegocio.</p>
        </a>

        <a href="{{ route('admin.catalog-items.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-archive-box class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Items Universales</h3>
            </div>
            <p class="text-sm text-gray-500">Crea lo que realmente se mostrara, vendera o reservara en el website.</p>
        </a>

        <a href="{{ route('admin.catalog-variants.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-adjustments-horizontal class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Variantes Universales</h3>
            </div>
            <p class="text-sm text-gray-500">Define presentaciones, tamanos, combos o versiones de cada item.</p>
        </a>
    </div>
</div>
@endsection
