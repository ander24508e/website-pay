@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
<div class="container mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">🏷️ Categorías</h2>
            <p class="text-gray-500 text-sm">Organiza productos y servicios por categoría</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
            + Nueva Categoría
        </a>
    </div>

    {{-- GRID DE 2 COLUMNAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Categorías de Servicios --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">

            {{-- Header de la tarjeta --}}
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-700">🛠️ Categorías de Servicios</h3>
            </div>

            {{-- SCROLL SOLO AQUÍ --}}
            <div class="overflow-auto max-h-[50vh]">

                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Servicios</th>
                            <th class="px-6 py-4">Acciones</th>
                        </thead>
                    <tbody class="divide-y">

                        @forelse($categories->where('type', 'service') as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $category->services->count() }}
                                </td>
                                <td class="px-6 py-4 flex gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-yellow-600 hover:text-yellow-800">
                                        Editar
                                    </a>

                                    <form method="POST"
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        onsubmit="return confirm('¿Eliminar esta categoría?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-600 hover:text-red-800">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">
                                    Sin categorías de servicios
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>

        </div>

        {{-- Categorías de Productos --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">

            {{-- Header de la tarjeta --}}
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-700">📦 Categorías de Productos</h3>
            </div>

            {{-- SCROLL SOLO AQUÍ --}}
            <div class="overflow-auto max-h-[50vh]">

                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Productos</th>
                            <th class="px-6 py-4">Acciones</th>
                        </thead>
                    <tbody class="divide-y">

                        @forelse($categories->where('type', 'product') as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $category->products->count() }}
                                </td>
                                <td class="px-6 py-4 flex gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-yellow-600 hover:text-yellow-800">
                                        Editar
                                    </a>

                                    <form method="POST"
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        onsubmit="return confirm('¿Eliminar esta categoría?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-600 hover:text-red-800">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">
                                    Sin categorías de productos
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