@extends('layouts.admin')

@section('title', 'Productos')

@section('content')

<div class="container mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📦 Productos</h2>
            <p class="text-gray-500 text-sm">Gestiona el inventario</p>
        </div>

        <a href="{{ route('admin.products.create') }}"
           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
            + Nuevo Producto
        </a>
    </div>

    {{-- CARD --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- SCROLL SOLO AQUÍ --}}
        <div class="overflow-auto max-h-[70vh]">

            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4">Imagen</th>
                        <th class="px-6 py-4">Nombre</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4">Proveedor</th>
                        <th class="px-6 py-4">Precio</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">

                            {{-- Imagen --}}
                            <td class="px-6 py-4">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}"
                                         class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                        📦
                                    </div>
                                @endif
                            </td>

                            {{-- Nombre --}}
                            <td class="px-6 py-4 font-medium">
                                {{ $product->name }}
                            </td>

                            {{-- Categoría --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ $product->category->name ?? '—' }}
                            </td>

                            {{-- Proveedor --}}
                            <td class="px-6 py-4 text-gray-500">
                                {{ $product->provider ?? '—' }}
                            </td>

                            {{-- Precio --}}
                            <td class="px-6 py-4 font-semibold">
                                ${{ number_format($product->price, 2) }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4">
                                @if($product->active)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                        Activo
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 flex gap-3">
                                <a href="{{ route('admin.products.show', $product) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    Ver
                                </a>

                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-yellow-600 hover:text-yellow-800">
                                    Editar
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('¿Eliminar?')">
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
                            <td colspan="7" class="text-center py-10 text-gray-400">
                                No hay productos registrados
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>

        {{-- PAGINACIÓN --}}
        @if($products->hasPages())
            <div class="p-4 border-t">
                {{ $products->links() }}
            </div>
        @endif

    </div>

</div>

@endsection