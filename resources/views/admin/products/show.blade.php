@extends('layouts.admin')

@section('title', 'Detalle Producto')

@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-800 transition">
        ← Volver
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
        <p class="text-gray-500 text-sm mt-1">Detalle del producto</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl">
    <div class="flex gap-8">

        {{-- Imagen --}}
        <div class="flex-shrink-0">
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-40 h-40 rounded-xl object-cover border border-gray-200">
            @else
                <div class="w-40 h-40 bg-gray-100 rounded-xl flex items-center justify-center text-4xl">
                    📦
                </div>
            @endif
        </div>

        {{-- Datos --}}
        <div class="flex-1 space-y-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                <p class="font-semibold text-gray-800">{{ $product->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Categoría</p>
                <p class="text-gray-700">{{ $product->category->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Proveedor</p>
                <p class="text-gray-700">{{ $product->provider ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Precio</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                @if($product->active)
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                @else
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Inactivo</span>
                @endif
            </div>
        </div>
    </div>

    @if($product->description)
    <div class="mt-6 pt-6 border-t border-gray-100">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Descripción</p>
        <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
    </div>
    @endif

    <div class="mt-8 flex gap-3">
        <a href="{{ route('admin.products.edit', $product) }}"
           class="bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium">
            Editar
        </a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
              onsubmit="return confirm('¿Eliminar este producto?')">
            @csrf
            @method('DELETE')
            <button class="bg-red-100 text-red-700 px-5 py-2.5 rounded-lg hover:bg-red-200 transition font-medium">
                Eliminar
            </button>
        </form>
    </div>
</div>

@endsection