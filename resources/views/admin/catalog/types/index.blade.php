@extends('layouts.admin')

@section('title', 'Tipos de Catalogo')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.catalog.index') }}"
                    class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                    <span aria-hidden="true">&larr;</span>
                </a>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Tipos de Catalogo</h2>
                    <p class="text-gray-500 text-sm mt-1">Define secciones como comida, bar, industrial o servicios.</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.catalog-types.index') }}" class="flex-1 max-w-xl">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, slug o descripcion..." class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>
        </form>

        <a href="{{ route('admin.catalog-types.create') }}"
            class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nuevo Tipo
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[760px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Nombre</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Slug</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Icono</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Categorias</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Items</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($types as $type)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-800">{{ $type->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($type->description, 70) ?: 'Sin descripcion adicional' }}</p>
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 font-mono text-xs">
                                {{ $type->slug ?: '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                {{ $type->icon ?: '-' }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $type->categories_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $type->items_count }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @if($type->active)
                                    <span class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.catalog-types.show', $type) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">Ver</a>
                                    <a href="{{ route('admin.catalog-types.edit', $type) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">Editar</a>
                                    <form method="POST" action="{{ route('admin.catalog-types.destroy', $type) }}" onsubmit="return confirm('¿Eliminar este tipo de catalogo?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium px-2 py-1">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400">No hay tipos de catalogo registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($types->hasPages())
            <div class="p-4 border-t">
                {{ $types->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
