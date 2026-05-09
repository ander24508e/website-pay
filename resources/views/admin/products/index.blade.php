@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
    <div class="container mx-auto px-4 sm:px-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cube class="w-8 h-8 text-gray-800" />
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Productos</h2>
                </div>
                <p class="text-gray-500 text-sm mt-1">Gestiona el inventario</p>
            </div>

            <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1 max-w-xl">
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, proveedor o categoria..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                </div>
            </form>

            <a href="{{ route('admin.products.create') }}"
                class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
                + Nuevo Producto
            </a>
        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            {{-- SCROLL SOLO AQUÍ (Horizontal en móvil) --}}
            <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                <table class="min-w-[700px] w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Imagen</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Categoría</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Proveedor</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Precio Desde</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Presentaciones</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Estado</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50">
                                {{-- Imagen --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                            class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                            <x-heroicon-o-photo class="w-5 h-5 text-gray-400" />
                                        </div>
                                    @endif
                                </td>

                                {{-- Nombre --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium text-gray-800">
                                    {{ $product->name }}
                                </td>

                                {{-- Categoría --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $product->category->name ?? '—' }}
                                </td>

                                {{-- Proveedor --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $product->provider ?? '—' }}
                                </td>

                                {{-- Precio --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                    ${{ number_format($product->display_price, 2) }}
                                </td>

                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $product->activeVariants->count() }}
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @if ($product->active)
                                        <span
                                            class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                            onsubmit="return confirm('¿Eliminar?')" class="inline">
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
                                <td colspan="8" class="text-center py-10 text-gray-400">
                                    No hay productos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if ($products->hasPages())
                <div class="p-4 border-t">
                    {{ $products->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
