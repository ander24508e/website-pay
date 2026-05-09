@extends('layouts.admin')

@section('title', 'CategorÃ­as')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <x-heroicon-o-tag class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">CategorÃ­as</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Organiza productos y servicios por categorÃ­a</p>
        </div>

        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex-1 max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o tipo de categoria..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.categories.create') }}"
           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nueva CategorÃ­a
        </a>
    </div>

    {{-- GRID DE 2 COLUMNAS --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- CategorÃ­as de Servicios --}}
        <div class="w-full lg:w-1/2 bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">

            <div class="px-4 sm:px-6 py-4 border-b bg-gray-50 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-wrench class="w-5 h-5 text-gray-600" />
                    <h3 class="font-semibold text-gray-700">CategorÃ­as de Servicios</h3>
                </div>
            </div>

            {{-- SCROLL VERTICAL EXCLUSIVO --}}
            <div class="overflow-y-auto max-h-[50vh]">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Servicios</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($categories->where('type', 'service') as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium text-gray-800">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $category->services->count() }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">
                                            Editar
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.categories.destroy', $category) }}"
                                              onsubmit="return confirm('Â¿Eliminar esta categorÃ­a?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">
                                    Sin categorÃ­as de servicios
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CategorÃ­as de Productos --}}
        <div class="w-full lg:w-1/2 bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">

            <div class="px-4 sm:px-6 py-4 border-b bg-gray-50 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-gray-600" />
                    <h3 class="font-semibold text-gray-700">CategorÃ­as de Productos</h3>
                </div>
            </div>

            {{-- SCROLL VERTICAL EXCLUSIVO --}}
            <div class="overflow-y-auto max-h-[50vh]">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Productos</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($categories->where('type', 'product') as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium text-gray-800">
                                    {{ $category->name }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $category->products->count() }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">
                                            Editar
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.categories.destroy', $category) }}"
                                              onsubmit="return confirm('Â¿Eliminar esta categorÃ­a?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">
                                    Sin categorÃ­as de productos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
