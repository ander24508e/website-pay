@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
<style>
    /* Contenedor principal que ocupará todo el espacio disponible */
    .categories-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Header fijo */
    .categories-header {
        flex-shrink: 0;
        margin-bottom: 1.5rem;
    }

    /* Grid de categorías que ocupa el espacio restante */
    .categories-grid {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
        min-height: 0;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tarjeta de categoría */
    .category-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    /* Header de la tarjeta */
    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        background: #f9fafb;
        flex-shrink: 0;
    }

    /* Contenedor de la tabla con scroll */
    .card-table-container {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        min-height: 0;
    }

    /* Estilos de tabla */
    .category-table {
        width: 100%;
        font-size: 0.875rem;
        border-collapse: collapse;
    }

    .category-table th {
        text-align: left;
        padding: 0.75rem 1.5rem;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: white;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 1px solid #f3f4f6;
    }

    .category-table td {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid #f9fafb;
    }

    .category-table tbody tr:hover {
        background: #f9fafb;
    }

    /* Scroll personalizado */
    .card-table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .card-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .card-table-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .card-table-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Mensaje vacío */
    .empty-message {
        padding: 2rem 1.5rem;
        text-align: center;
        color: #9ca3af;
        font-size: 0.875rem;
    }
</style>

<div class="categories-container">
    <!-- Header -->
    <div class="categories-header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">🏷️ Categorías</h2>
                <p class="text-gray-500 text-sm mt-1">Organiza productos y servicios por categoría</p>
            </div>
            <a href="{{ route('admin.categories.create') }}"
               class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
                + Nueva Categoría
            </a>
        </div>
    </div>

    <!-- Grid de categorías -->
    <div class="categories-grid">
        <!-- Categorías de Servicios -->
        <div class="category-card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-700">🛠️ Categorías de Servicios</h3>
            </div>
            <div class="card-table-container">
                <table class="category-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Servicios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories->where('type', 'service') as $category)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $category->name }}</td>
                            <td class="text-gray-500">{{ $category->services->count() }}</td>
                            <td>
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-yellow-600 hover:text-yellow-700 text-xs font-medium">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta categoría?')"
                                          class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="text-red-500 hover:text-red-600 text-xs font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-message">
                                Sin categorías de servicios.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Categorías de Productos -->
        <div class="category-card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-700">📦 Categorías de Productos</h3>
            </div>
            <div class="card-table-container">
                <table class="category-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories->where('type', 'product') as $category)
                        <tr>
                            <td class="font-medium text-gray-800">{{ $category->name }}</td>
                            <td class="text-gray-500">{{ $category->products->count() }}</td>
                            <td>
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-yellow-600 hover:text-yellow-700 text-xs font-medium">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta categoría?')"
                                          class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="text-red-500 hover:text-red-600 text-xs font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-message">
                                Sin categorías de productos.
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