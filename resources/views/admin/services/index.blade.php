@extends('layouts.admin')

@section('title', 'Servicios')

@section('content')
    <div class="container mx-auto px-4 sm:px-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">🛠️ Servicios</h2>
                <p class="text-gray-500 text-sm">Gestiona los servicios del carwash</p>
            </div>
            <a href="{{ route('admin.services.create') }}"
                class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
                + Nuevo Servicio
            </a>
        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
                <table class="min-w-[600px] w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Imagen</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Nombre</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Categoría</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Precio</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Estado</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($services as $service)
                            <tr class="hover:bg-gray-50">

                                {{-- Imagen --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @if ($service->image)
                                        <img src="{{ Storage::url($service->image) }}"
                                            class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-xl">
                                            <x-bi-image />
                                        </div>
                                    @endif
                                </td>

                                {{-- Nombre --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium text-gray-800">
                                    {{ $service->name }}
                                </td>

                                {{-- Categoría --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                    {{ $service->category->name ?? '—' }}
                                </td>

                                {{-- Precio --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                    ${{ number_format($service->price, 2) }}
                                </td>

                                {{-- Estado --}}
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @if ($service->active)
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
                                        <a href="{{ route('admin.services.show', $service) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.services.edit', $service) }}"
                                            class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                            onsubmit="return confirm('¿Eliminar este servicio?')" class="inline">
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
                                <td colspan="6" class="text-center py-10 text-gray-400">
                                    No hay servicios registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if ($services->hasPages())
                <div class="p-4 border-t">
                    {{ $services->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
