@extends('layouts.admin')

@section('title', 'Detalle Producto o Servicio')

@section('content')
@php
    $returnUrl = $returnUrl ?? route('admin.catalog-items.index', ['catalog_type_id' => $catalogItem->catalog_type_id]);
    $returnContext = $returnContext ?? [];
    $isServiceContext = ($catalogItem->type?->business_model ?? 'services') === \App\Models\CatalogType::BUSINESS_MODEL_SERVICES;
@endphp

<div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-4">
        <a href="{{ route('admin.catalog.index') }}" class="hover:text-gray-600 transition">Catálogo</a>
        <span>/</span>
        @if($catalogItem->type)
            <a href="{{ route('admin.catalog-types.show', $catalogItem->type) }}" class="hover:text-gray-600 transition">{{ $catalogItem->type->name }}</a>
            <span>/</span>
        @endif
        @if($catalogItem->category)
            <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogItem->catalog_type_id, 'catalog_category_id' => $catalogItem->catalog_category_id]) }}" class="hover:text-gray-600 transition">{{ $catalogItem->category->name }}</a>
            <span>/</span>
        @endif
        <span class="text-gray-600 font-semibold">{{ $catalogItem->name }}</span>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ $returnUrl }}"
           class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogItem->name }}</h2>
            <p class="text-gray-400 text-sm">Detalle del producto o servicio</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ $catalogItem->type ? route('admin.catalog-types.show', $catalogItem->type) : route('admin.catalog.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Sección</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogItem->type?->name ?? 'Sin sección' }}</p>
            <p class="text-xs text-gray-400 mt-1">Volver a la sección</p>
        </a>
        <a href="{{ $catalogItem->category ? route('admin.catalog-items.index', ['catalog_type_id' => $catalogItem->catalog_type_id, 'catalog_category_id' => $catalogItem->catalog_category_id]) : route('admin.catalog-items.index', ['catalog_type_id' => $catalogItem->catalog_type_id]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Categoría</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $catalogItem->category?->name ?? 'Sin categoría' }}</p>
            <p class="text-xs text-gray-400 mt-1">Ver contexto del producto o servicio</p>
        </a>
        <a href="{{ $isServiceContext ? route('admin.catalog-service-prices.create', ['catalogItem' => $catalogItem, ...$returnContext]) : route('admin.catalog-variants.index', ['catalog_item_id' => $catalogItem->id]) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-gray-300 transition">
            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">{{ $isServiceContext ? 'Precios' : 'Presentaciones' }}</p>
            <p class="text-lg font-bold text-gray-800 mt-2">{{ $isServiceContext ? $catalogItem->vehicleTypePrices->count() : $catalogItem->variants->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $isServiceContext ? 'Gestionar precios por vehiculo' : 'Gestionar presentaciones' }}</p>
        </a>
    </div>

    <div class="mx-auto w-full max-w-4xl">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-1">
                    <img src="{{ $catalogItem->image_url }}" alt="{{ $catalogItem->name }}" class="w-full h-64 rounded-xl object-cover bg-gray-50 border border-gray-200">
                </div>

                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                        <p class="font-semibold text-gray-800 break-words">{{ $catalogItem->name }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Sección</p>
                            <p class="text-gray-700">{{ $catalogItem->type?->name ?? 'Sin sección' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Categoría</p>
                            <p class="text-gray-700">{{ $catalogItem->category?->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                            <p class="text-gray-700 font-mono text-sm">{{ $catalogItem->slug ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Precio visible</p>
                            <p class="text-gray-700 font-semibold">${{ number_format($catalogItem->display_price, 2) }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Descripción</p>
                        <p class="text-gray-700">{{ $catalogItem->description ?: 'Sin descripción adicional.' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($catalogItem->active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Activo</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">Oculto</span>
                        @endif
                        @if($catalogItem->featured)
                            <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">Destacado</span>
                        @endif
                        @if($catalogItem->purchasable)
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Se puede vender</span>
                        @endif
                        @if($catalogItem->reservable)
                            <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-medium">Se puede reservar</span>
                        @endif
                        @if($catalogItem->uses_inventory)
                            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-medium">Controla stock</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $isServiceContext ? 'Precios' : 'Presentaciones' }}</p>
                    <p class="text-gray-700 font-semibold">{{ $isServiceContext ? $catalogItem->vehicleTypePrices->count() : $catalogItem->variants->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Origen anterior</p>
                    <p class="text-gray-700">{{ $catalogItem->legacy_source_type && $catalogItem->legacy_source_id ? $catalogItem->legacy_source_type . ' #' . $catalogItem->legacy_source_id : 'Aún no migrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $isServiceContext ? 'Reserva' : 'Control de inventario' }}</p>
                    <p class="text-gray-700 font-semibold">{{ $isServiceContext ? ($catalogItem->reservable ? 'Activa' : 'No aplica') : ($catalogItem->uses_inventory ? 'Activo' : 'No aplica') }}</p>
                </div>
            </div>

            @if ($isServiceContext)
                <div class="mb-8">
                    <div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Precios por vehiculo</p>
                            <p class="text-xs text-gray-400">Gestiona precios, duracion e insumos por tipo de vehiculo.</p>
                        </div>
                        <a href="{{ route('admin.catalog-service-prices.create', ['catalogItem' => $catalogItem, ...$returnContext]) }}"
                           class="w-full bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium text-center sm:w-auto">
                            + Precio por vehiculo
                        </a>
                    </div>
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <div class="md:hidden divide-y divide-gray-100">
                            @forelse ($catalogItem->vehicleTypePrices as $vehiclePrice)
                                <article class="p-4 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 break-words">{{ $vehiclePrice->vehicleType?->name ?? 'Tipo eliminado' }}</p>
                                            <p class="text-xs text-gray-400">{{ $vehiclePrice->duration_minutes ? $vehiclePrice->duration_minutes . ' min' : 'Sin duracion' }}</p>
                                        </div>
                                        @if ($vehiclePrice->active)
                                            <span class="shrink-0 bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activo</span>
                                        @else
                                            <span class="shrink-0 bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculto</span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                                            <p class="font-semibold text-gray-800">${{ number_format((float) $vehiclePrice->price, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase text-gray-400 font-semibold">Insumos</p>
                                            <p class="font-semibold text-gray-800">{{ $vehiclePrice->supplies->count() }}</p>
                                        </div>
                                    </div>

                                    @if ($vehiclePrice->description)
                                        <p class="text-sm text-gray-500">{{ $vehiclePrice->description }}</p>
                                    @endif

                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.catalog-service-prices.edit', $vehiclePrice) }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-800">Editar</a>
                                        <form method="POST" action="{{ route('admin.catalog-service-prices.destroy', $vehiclePrice) }}" onsubmit="return confirm('¿Eliminar este precio por vehiculo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-sm font-medium text-red-600 hover:text-red-800">Eliminar</button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="px-4 py-8 text-center text-gray-400">Este servicio aun no tiene precios por vehiculo.</div>
                            @endforelse
                        </div>

                        <div class="hidden md:block">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3">Tipo de vehiculo</th>
                                        <th class="px-4 py-3">Precio</th>
                                        <th class="px-4 py-3">Duracion</th>
                                        <th class="px-4 py-3">Insumos</th>
                                        <th class="px-4 py-3">Estado</th>
                                        <th class="px-4 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse ($catalogItem->vehicleTypePrices as $vehiclePrice)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <p class="font-medium text-gray-800">{{ $vehiclePrice->vehicleType?->name ?? 'Tipo eliminado' }}</p>
                                                @if ($vehiclePrice->description)
                                                    <p class="text-xs text-gray-400">{{ $vehiclePrice->description }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-gray-800">
                                                ${{ number_format((float) $vehiclePrice->price, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $vehiclePrice->duration_minutes ? $vehiclePrice->duration_minutes . ' min' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-700">
                                                {{ $vehiclePrice->supplies->count() }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($vehiclePrice->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activo</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculto</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('admin.catalog-service-prices.edit', $vehiclePrice) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Editar</a>
                                                    <form method="POST" action="{{ route('admin.catalog-service-prices.destroy', $vehiclePrice) }}" onsubmit="return confirm('¿Eliminar este precio por vehiculo?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">Este servicio aun no tiene precios por vehiculo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @unless ($isServiceContext)
            <div class="mb-8">
                <div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Presentaciones</p>
                        <p class="text-xs text-gray-400">Gestiona formatos, precios y stock por presentacion.</p>
                    </div>
                    <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $catalogItem->id]) }}"
                       class="w-full bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium text-center sm:w-auto">
                        + Nueva Presentación
                    </a>
                </div>

                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="md:hidden divide-y divide-gray-100">
                        @forelse($catalogItem->variants as $variant)
                            <article class="p-4 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 break-words">{{ $variant->name }}</p>
                                        <p class="text-xs text-gray-400 break-words">{{ $variant->sku ?: 'Sin SKU' }}</p>
                                    </div>
                                    <div class="shrink-0 flex flex-wrap justify-end gap-1">
                                        @if($variant->active)
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activa</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculta</span>
                                        @endif
                                        @if($variant->is_default)
                                            <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Base</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                                        <p class="font-semibold text-gray-800">{{ $variant->price !== null ? '$' . number_format((float) $variant->price, 2) : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Stock</p>
                                        <p class="font-semibold text-gray-800">{{ $variant->stock ?? 0 }}</p>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.catalog-variants.edit', ['catalogVariant' => $variant, 'redirect_to_item' => 1]) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Editar</a>
                                </div>
                            </article>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400">Este producto o servicio aún no tiene presentaciones.</div>
                        @endforelse
                    </div>

                    <div class="hidden md:block">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-3">Presentación</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Precio</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($catalogItem->variants as $variant)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800">{{ $variant->name }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                                            {{ $variant->sku ?: 'Sin SKU' }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            {{ $variant->price !== null ? '$' . number_format((float) $variant->price, 2) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-800">
                                            {{ $variant->stock ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @if($variant->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium">Activa</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">Oculta</span>
                                                @endif
                                                @if($variant->is_default)
                                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">Base</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('admin.catalog-variants.edit', ['catalogVariant' => $variant, 'redirect_to_item' => 1]) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Editar</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-400">Este producto o servicio aún no tiene presentaciones.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endunless

            <div class="flex flex-col gap-3 pt-2 border-t border-gray-100 sm:flex-row">
                <a href="{{ route('admin.catalog-items.edit', ['catalogItem' => $catalogItem, ...$returnContext]) }}"
                   class="w-full bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm text-center sm:w-auto">
                    Editar
                </a>
                @unless ($isServiceContext)
                    <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $catalogItem->id]) }}"
                       class="w-full bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium text-sm text-center sm:w-auto">
                        + Presentación
                    </a>
                @endunless
                <form action="{{ route('admin.catalog-items.destroy', $catalogItem) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este producto o servicio?')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200 sm:w-auto">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
