@extends('layouts.admin')

@section('title', 'Catalogo')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="mb-6">
        <div class="flex items-center gap-2">
            <x-heroicon-o-squares-2x2 class="w-8 h-8 text-gray-800" />
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catalogo</h2>
        </div>
        <p class="text-gray-500 text-sm mt-1">Centro de control del catalogo publico y base para el catalogo universal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <a href="{{ route('admin.catalog-types.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-squares-plus class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Tipos</h3>
            </div>
            <p class="text-sm text-gray-500">Crea secciones universales como comida, bebidas, bar o industrial.</p>
        </a>

        <a href="{{ route('admin.catalog-categories.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-tag class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Categorias Universales</h3>
            </div>
            <p class="text-sm text-gray-500">Organiza items dentro de cada tipo del nuevo catalogo.</p>
        </a>

        <a href="{{ route('admin.catalog-items.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-archive-box class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Items Universales</h3>
            </div>
            <p class="text-sm text-gray-500">Crea la pieza central del catalogo futuro para cualquier negocio.</p>
        </a>

        <a href="{{ route('admin.products.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-cube class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Productos</h3>
            </div>
            <p class="text-sm text-gray-500">Gestiona articulos fisicos y sus variantes actuales.</p>
        </a>

        <a href="{{ route('admin.services.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-wrench class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Servicios</h3>
            </div>
            <p class="text-sm text-gray-500">Administra servicios visibles y reservables del website.</p>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-tag class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Categorias</h3>
            </div>
            <p class="text-sm text-gray-500">Organiza productos y servicios mientras se prepara la version universal.</p>
        </a>

        <a href="{{ route('admin.banners.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3 mb-3">
                <x-heroicon-o-rectangle-group class="w-6 h-6 text-gray-800" />
                <h3 class="font-semibold text-gray-800">Banners</h3>
            </div>
            <p class="text-sm text-gray-500">Controla la comunicacion visual del landing page.</p>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Ruta del Catalogo Universal</h3>
        <div class="space-y-3 text-sm text-gray-600">
            <p>1. Mantener estable el flujo actual de productos, servicios, carrito y reservas.</p>
            <p>2. Crear la nueva capa universal de tipos, categorias e items reutilizables.</p>
            <p>3. Hacer que el website lea progresivamente el nuevo catalogo sin romper lo existente.</p>
            <p>4. Migrar datos y retirar despues la estructura antigua.</p>
        </div>

        <div class="mt-5 rounded-lg bg-gray-50 border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-700">Documento base</p>
            <p class="text-sm text-gray-500 mt-1">La arquitectura y fases del cambio quedaron documentadas en <code>docs/catalogo-universal-plan.md</code>.</p>
        </div>
    </div>
</div>
@endsection
