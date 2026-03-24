@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">🏷️ Categorías</h2>
        <p class="text-gray-500 text-sm mt-1">Organiza productos y servicios por categoría</p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
        + Nueva Categoría
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Categorías de Servicios --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-700">🛠️ Categorías de Servicios</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Nombre</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Servicios</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories->where('type', 'service') as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $category->services->count() }}</td>
                    <td class="px-6 py-3">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 hover:underline text-xs">Editar</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar esta categoría?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-6 text-center text-gray-400 text-sm">Sin categorías de servicios.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Categorías de Productos --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-700">📦 Categorías de Productos</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Nombre</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Productos</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-semibold text-xs">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories->where('type', 'product') as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $category->products->count() }}</td>
                    <td class="px-6 py-3">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 hover:underline text-xs">Editar</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar esta categoría?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-6 text-center text-gray-400 text-sm">Sin categorías de productos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection