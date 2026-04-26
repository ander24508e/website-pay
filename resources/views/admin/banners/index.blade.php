@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
<div class="container mx-auto px-4 sm:px-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <x-heroicon-o-rectangle-stack class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Banners</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Gestiona el carrusel del landing page</p>
        </div>

        <a href="{{ route('admin.banners.create') }}"
           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
            + Nuevo Banner
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto overflow-y-auto max-h-[70vh]">
            <table class="min-w-[760px] w-full text-sm text-left">
                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Imagen</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Titulo</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Boton</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Orden</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Estado</th>
                        <th class="px-4 sm:px-6 py-3 sm:py-4">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <img src="{{ $banner->imagen_url }}" alt="{{ $banner->titulo ?: 'Banner' }}" class="w-16 h-10 rounded object-cover border border-gray-200">
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <p class="font-medium text-gray-800">{{ $banner->titulo ?: 'Sin titulo' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($banner->texto, 70) ?: 'Sin texto adicional' }}</p>
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500">
                                @if($banner->boton_texto)
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">
                                        {{ $banner->boton_texto }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800">
                                {{ $banner->orden }}
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                @if($banner->activo)
                                    <span class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                                @endif
                            </td>

                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.banners.show', $banner) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium px-2 py-1">
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.banners.edit', $banner) }}"
                                       class="text-yellow-600 hover:text-yellow-800 text-sm font-medium px-2 py-1">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                          onsubmit="return confirm('Eliminar este banner?')" class="inline">
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
                            <td colspan="6" class="text-center py-10 text-gray-400">No hay banners registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="p-4 border-t">
                {{ $banners->links() }}
            </div>
        @endif

    </div>
</div>
@endsection