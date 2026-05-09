@extends('layouts.admin')

@section('title', 'Detalle Producto')

@section('content')

{{-- HEADER --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.products.index') }}"
       class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
        ←
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">📦 {{ $product->name }}</h2>
        <p class="text-gray-400 text-sm">Detalle del producto</p>
    </div>
</div>

{{-- LAYOUT DOS COLUMNAS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna izquierda --}}
    <div class="lg:col-span-1 flex flex-col gap-6">

        {{-- Card imagen --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Imagen</p>
            <div class="flex flex-col items-center">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-40 h-40 rounded-xl object-cover border border-gray-200 shadow-sm">
                @else
                    <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center text-5xl border-2 border-dashed border-gray-200">
                        📦
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Sin imagen</p>
                @endif
            </div>
        </div>

        {{-- Card estado --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Estado</p>
            <div class="flex items-center gap-3">
                @if($product->active)
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Activo</p>
                        <p class="text-xs text-gray-400">Visible en el catálogo</p>
                    </div>
                @else
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Inactivo</p>
                        <p class="text-xs text-gray-400">Oculto del catálogo</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card acciones --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Acciones</p>
            <div class="flex flex-col gap-3">
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="w-full bg-gray-900 text-white py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm text-center">
                    ✏️ Editar Producto
                </a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este producto definitivamente?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full bg-red-50 text-red-600 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                        🗑 Eliminar
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Columna derecha --}}
    <div class="lg:col-span-2 flex flex-col gap-6">

        {{-- Card info principal --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-5">Información del Producto</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Nombre</p>
                    <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Categoría</p>
                    <p class="text-gray-700">
                        @if($product->category)
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Proveedor</p>
                    <p class="text-gray-700">{{ $product->provider ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Precio</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($product->display_price, 2) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Precio minimo entre presentaciones activas</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Presentaciones</p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Nombre</th>
                            <th class="py-2 pr-4">Presentacion</th>
                            <th class="py-2 pr-4">Especificacion</th>
                            <th class="py-2 pr-4">Precio</th>
                            <th class="py-2 pr-4">Stock</th>
                            <th class="py-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($product->variants as $variant)
                            <tr>
                                <td class="py-2 pr-4 text-gray-800">{{ $variant->name }}</td>
                                <td class="py-2 pr-4 text-gray-700">{{ $variant->presentation ?? '—' }}</td>
                                <td class="py-2 pr-4 text-gray-700">{{ $variant->specification ?? '—' }}</td>
                                <td class="py-2 pr-4 font-semibold text-gray-800">${{ number_format($variant->price, 2) }}</td>
                                <td class="py-2 pr-4 text-gray-700">{{ $variant->stock ?? '—' }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $variant->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $variant->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-3 text-gray-400">Sin presentaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card descripción --}}
        @if($product->description)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Descripción</p>
            <p class="text-gray-700 leading-relaxed text-sm">{{ $product->description }}</p>
        </div>
        @endif

        {{-- Card metadatos --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Información del registro</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Creado</p>
                    <p class="text-sm text-gray-700 font-medium">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Última actualización</p>
                    <p class="text-sm text-gray-700 font-medium">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
