@extends('layouts.admin')

@section('title', 'Productos')

@section('content')
<style>
    /* Contenedor principal */
    .products-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Header fijo */
    .products-header {
        flex-shrink: 0;
        margin-bottom: 1.5rem;
    }

    /* Contenedor de la tarjeta que ocupa el espacio restante */
    .products-card {
        flex: 1;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    /* Contenedor de la tabla con scroll */
    .table-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        min-height: 0;
    }

    /* Estilos de tabla */
    .products-table {
        width: 100%;
        font-size: 0.875rem;
        border-collapse: collapse;
    }

    .products-table th {
        text-align: left;
        padding: 1rem 1.5rem;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 1px solid #e5e7eb;
    }

    .products-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .products-table tbody tr:hover {
        background: #f9fafb;
    }

    /* Scroll personalizado */
    .table-wrapper::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Paginación */
    .pagination-container {
        flex-shrink: 0;
        padding: 1rem 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    /* Imagen de producto */
    .product-image {
        width: 48px;
        height: 48px;
        border-radius: 0.5rem;
        object-fit: cover;
    }

    .product-placeholder {
        width: 48px;
        height: 48px;
        background: #f3f4f6;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }
</style>

<div class="products-container">
    <!-- Header -->
    <div class="products-header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📦 Productos</h2>
                <p class="text-gray-500 text-sm mt-1">Gestiona el inventario de productos</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
               class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
                + Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Tarjeta de productos -->
    <div class="products-card">
        <div class="table-wrapper">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td>
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="product-image">
                            @else
                                <div class="product-placeholder">
                                    📦
                                </div>
                            @endif
                        </td>
                        <td class="font-medium text-gray-800">{{ $product->name }}</td>
                        <td class="text-gray-500">{{ $product->category->name ?? '—' }}</td>
                        <td class="text-gray-500">{{ $product->provider ?? '—' }}</td>
                        <td class="font-semibold text-gray-800">${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.show', $product) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">Ver</a>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Editar</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este producto?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-xs font-medium">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-message" style="padding: 3rem; text-align: center; color: #9ca3af;">
                            No hay productos registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="pagination-container">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection