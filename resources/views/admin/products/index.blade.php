@extends('layouts.admin')

@section('title', 'Productos')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">📦 Productos</h2>
        <p class="text-gray-500 text-sm mt-1">Gestiona el inventario de productos</p>
    </div>
    <a href="{{ route('admin.products.create') }}"
       class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
        + Nuevo Producto
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Imagen</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Nombre</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Categoría</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Proveedor</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Precio</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Estado</th>
                <th class="text-left px-6 py-4 text-gray-600 font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-12 h-12 rounded-lg object-cover">
                    @else
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                            📦
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $product->category->name ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $product->provider ?? '—' }}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">${{ number_format($product->price, 2) }}</td>
                <td class="px-6 py-4">
                    @if($product->active)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Inactivo</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.show', $product) }}"
                           class="text-blue-600 hover:underline text-xs">Ver</a>
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-yellow-600 hover:underline text-xs">Editar</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                    No hay productos registrados aún.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection