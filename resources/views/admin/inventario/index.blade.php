@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')
    @php
        $hasProductTypes = $productTypes->isNotEmpty();
        $primaryProductUrl = $hasProductTypes
            ? route(
                'admin.catalog-items.create',
                array_filter(['inventory' => 1, 'catalog_type_id' => $selectedTypeId ?: null]),
            )
            : null;
        $newMovementUrl = $hasProductTypes ? route('admin.inventario.create') : null;
        $moreActions = $hasProductTypes
            ? [
                [
                    'route' => route('admin.inventario.locations'),
                    'label' => 'Ubicaciones',
                    'title' => 'Ubicaciones',
                    'icon' => 'building-storefront',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.suppliers'),
                    'label' => 'Proveedores',
                    'title' => 'Proveedores',
                    'icon' => 'users',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.purchases'),
                    'label' => 'Compras',
                    'title' => 'Compras',
                    'icon' => 'shopping-bag',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.transfers'),
                    'label' => 'Transferir',
                    'title' => 'Transferencias',
                    'icon' => 'arrows-right-left',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.returns'),
                    'label' => 'Devolver',
                    'title' => 'Devoluciones',
                    'icon' => 'arrow-path-rounded-square',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.counts'),
                    'label' => 'Conteo',
                    'title' => 'Conteo físico',
                    'icon' => 'clipboard-document-check',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.reports'),
                    'label' => 'Reportes',
                    'title' => 'Reportes',
                    'icon' => 'chart-bar',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.periods'),
                    'label' => 'Cierres',
                    'title' => 'Cierres',
                    'icon' => 'lock-closed',
                    'primary' => false,
                ],
                [
                    'route' => route('admin.inventario.import'),
                    'label' => 'Importar',
                    'title' => 'Importar CSV',
                    'icon' => 'arrow-up-tray',
                    'primary' => false,
                ],
                [
                    'route' => route(
                        'admin.inventario.export',
                        array_filter(['catalog_type_id' => $selectedTypeId ?: null]),
                    ),
                    'label' => 'Exportar',
                    'title' => 'Exportar CSV',
                    'icon' => 'arrow-down-tray',
                ],
            ]
            : [];
        $panelClass = 'bg-white rounded-lg border border-gray-200/70 shadow-sm overflow-hidden min-w-0';
    @endphp

    <div class="w-full max-w-[1600px] mx-auto px-3 sm:px-5 xl:px-8 pb-8">
        <div class="mb-6 space-y-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex min-w-0 items-start gap-3">
                    <span
                        class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-900 shadow-sm">
                        <x-heroicon-o-archive-box class="h-6 w-6" />
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-2xl sm:text-3xl font-medium leading-tight text-gray-900">Inventario</h3>
                        <p class="mt-1.5 max-w-3xl text-sm text-gray-500">Gestiona el stock de tus productos.</p>
                    </div>
                </div>

                @if ($hasProductTypes)
                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ $newMovementUrl }}"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                            <x-heroicon-o-archive-box class="h-5 w-5" />
                            Movimiento
                        </a>

                        <details class="relative">
                            <summary
                                class="inline-flex h-11 cursor-pointer list-none items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 [&::-webkit-details-marker]:hidden"
                                aria-label="Más acciones">
                                <x-heroicon-o-ellipsis-horizontal class="h-5 w-5" />
                                <span>Más acciones</span>
                            </summary>
                            <div
                                class="absolute right-0 z-20 mt-2 w-64 overflow-hidden rounded-lg border border-gray-200 bg-white py-2 shadow-lg">
                                @foreach ($moreActions as $tool)
                                    <a href="{{ $tool['route'] }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                                        title="{{ $tool['title'] }}">
                                        @switch($tool['icon'])
                                            @case('building-storefront')
                                                <x-heroicon-o-building-storefront class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('users')
                                                <x-heroicon-o-users class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('shopping-bag')
                                                <x-heroicon-o-shopping-bag class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('arrows-right-left')
                                                <x-heroicon-o-arrows-right-left class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('arrow-path-rounded-square')
                                                <x-heroicon-o-arrow-path-rounded-square class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('clipboard-document-check')
                                                <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('chart-bar')
                                                <x-heroicon-o-chart-bar class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('lock-closed')
                                                <x-heroicon-o-lock-closed class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('arrow-up-tray')
                                                <x-heroicon-o-arrow-up-tray class="h-4 w-4 text-gray-400" />
                                            @break

                                            @case('arrow-down-tray')
                                                <x-heroicon-o-arrow-down-tray class="h-4 w-4 text-gray-400" />
                                            @break
                                        @endswitch
                                        <span>{{ $tool['title'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </details>

                        <a href="{{ $primaryProductUrl }}"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700">
                            <x-heroicon-o-plus class="h-5 w-5" />
                            Agregar producto
                        </a>
                    </div>
                @endif
            </div>

            @if ($hasProductTypes)
                <form method="GET" action="{{ route('admin.inventario.index') }}" class="relative">
                    <input type="hidden" name="catalog_type_id" value="{{ $selectedTypeId ?: '' }}">
                    <x-heroicon-o-magnifying-glass
                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input type="search" name="q" value="{{ request('q') }}"
                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pl-11 pr-24 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                        placeholder="Buscar producto, negocio, categoría, presentación o SKU">
                    <button
                        class="absolute right-1.5 top-1/2 inline-flex h-9 -translate-y-1/2 items-center justify-center rounded-md bg-gray-100 px-4 text-xs font-semibold text-gray-700 transition hover:bg-gray-200">
                        Buscar
                    </button>
                </form>
            @endif
        </div>

        @unless ($hasProductTypes)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 sm:p-10 text-center max-w-3xl mx-auto">
                <div class="w-14 h-14 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-building-storefront class="w-7 h-7" />
                </div>
                <h3 class="text-xl font-bold text-gray-800">Inventario necesita un negocio de productos</h3>
                <p class="text-sm text-gray-500 mt-2 max-w-xl mx-auto">
                    Para controlar stock, primero crea un negocio configurado como productos. Los servicios no aparecen aquí
                    porque no tienen existencias físicas.
                </p>
                <div class="mt-6 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('admin.catalog-types.create', ['business_model' => 'products']) }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition text-sm font-semibold">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        Crear negocio de productos
                    </a>
                    <a href="{{ route('admin.catalog.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition text-sm font-semibold">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                        Ir a catálogo
                    </a>
                </div>
            </div>
        @else
            <div class="mb-6 overflow-x-auto rounded-lg border border-gray-200/70 bg-white p-2 shadow-sm">
                <div class="flex min-w-max items-center gap-2">
                    <a href="{{ route('admin.inventario.index', array_filter(['q' => request('q')])) }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold transition {{ $selectedTypeId === 0 ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Todos
                    </a>
                    @foreach ($productTypes as $type)
                        <a href="{{ route('admin.inventario.index', array_filter(['catalog_type_id' => $type->id, 'q' => request('q')])) }}"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold transition {{ $selectedTypeId === $type->id ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $type->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Presentaciones</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['variants'] }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Unidades</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['units'] }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Valor</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2 break-words">
                        {{ $canViewCosts ? '$' . number_format($inventoryStats['value'], 2) : 'Restringido' }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Agotados</p>
                    <p class="text-2xl font-bold text-red-700 mt-2">{{ $inventoryStats['out'] }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Bajo stock</p>
                    <p class="text-2xl font-bold text-amber-700 mt-2">{{ $inventoryStats['low'] }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200/70 shadow-sm p-4 min-h-24">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Sin costo</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $inventoryStats['no_cost'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_380px] gap-5 xl:gap-6">
                <section class="{{ $panelClass }}">
                    <div
                        class="px-4 sm:px-5 py-4 border-b border-gray-200/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="font-semibold text-gray-900">Stock actual de productos</h3>
                            <p class="text-xs text-gray-400 mt-1">Productos físicos registrados para el carwash.</p>
                        </div>
                        <span class="text-xs font-semibold text-gray-400">{{ $products->total() }} registros</span>
                    </div>

                    <div class="lg:hidden space-y-3 p-4">
                        @forelse($products as $product)
                            @php
                                $variant = $product->variants->first();
                                $variantName = $variant ? $variant->name : 'Producto base';
                                $variantSku = $variant ? $variant->sku : null;
                                $variantStock = $variant ? (int) ($variant->stock ?? 0) : 0;
                                $variantMinStock = $variant ? (int) ($variant->min_stock ?? 0) : 0;
                                $isOutOfStock = $variantStock <= 0;
                                $isLowStock =
                                    !$isOutOfStock && $variantMinStock > 0 && $variantStock <= $variantMinStock;
                            @endphp
                            <article class="rounded-lg border border-gray-200/70 p-4 space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 break-words">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400 break-words">{{ $product->type->name ?? '-' }} ·
                                            {{ $product->category->name ?? 'Sin categoría' }}</p>
                                    </div>
                                    <span
                                        class="shrink-0 {{ $isOutOfStock ? 'bg-red-100 text-red-700' : ($isLowStock ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }} px-2.5 py-1 rounded-full text-xs font-semibold">
                                        {{ $isOutOfStock ? 'Agotado' : ($isLowStock ? 'Bajo' : 'Activo') }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Presentación</p>
                                        <p class="text-gray-700 break-words">{{ $variantName }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">SKU</p>
                                        <p class="font-mono text-xs text-gray-700 break-words">{{ $variantSku ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Stock</p>
                                        <p class="font-semibold text-gray-900">{{ $variantStock }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Mínimo</p>
                                        <p class="font-semibold text-gray-900">{{ $variantMinStock }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                                        <p class="font-semibold text-gray-900">
                                            ${{ number_format($variant?->price ?? ($product->base_price ?? 0), 2) }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($variant)
                                        <a href="{{ route('admin.inventario.kardex', $variant) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                            <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                            Kardex
                                        </a>
                                    @endif
                                    <button type="button"
                                        class="open-stock-modal inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-semibold"
                                        data-product-id="{{ $product->id }}" data-variant-id="{{ $variant?->id }}"
                                        data-product-name="{{ $product->name }}" data-variant-name="{{ $variantName }}"
                                        data-current-stock="{{ $variantStock }}" title="Gestionar stock"
                                        aria-label="Gestionar stock de {{ $product->name }}">
                                        <x-heroicon-o-archive-box class="w-4 h-4" />
                                        Stock
                                    </button>
                                </div>
                            </article>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400">No hay productos para este negocio o búsqueda.
                            </div>
                        @endforelse
                    </div>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-[960px] w-full text-sm">
                            <thead class="bg-gray-50 border-b text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Producto</th>
                                    <th class="px-4 py-3 text-left">Negocio</th>
                                    <th class="px-4 py-3 text-left">Categoría</th>
                                    <th class="px-4 py-3 text-left">Presentación</th>
                                    <th class="px-4 py-3 text-center">Stock</th>
                                    <th class="px-4 py-3 text-center">Mín.</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($products as $product)
                                    @php
                                        $variant = $product->variants->first();
                                        $variantName = $variant ? $variant->name : 'Producto base';
                                        $variantSku = $variant ? $variant->sku : null;
                                        $variantStock = $variant ? (int) ($variant->stock ?? 0) : 0;
                                        $variantMinStock = $variant ? (int) ($variant->min_stock ?? 0) : 0;
                                        $isOutOfStock = $variantStock <= 0;
                                        $isLowStock =
                                            !$isOutOfStock && $variantMinStock > 0 && $variantStock <= $variantMinStock;
                                    @endphp
                                    <tr class="hover:bg-gray-50/70">
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-gray-900 max-w-[240px] truncate">{{ $product->name }}
                                            </p>
                                            <p class="text-xs text-gray-400 max-w-[240px] truncate">
                                                {{ $product->description ?: 'Sin descripción' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            <p class="max-w-[170px] truncate">{{ $product->type->name ?? '-' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            <p class="max-w-[170px] truncate">
                                                {{ $product->category->name ?? 'Sin categoría' }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-gray-700 max-w-[190px] truncate">{{ $variantName }}</p>
                                            <p class="text-gray-500 font-mono text-xs max-w-[190px] truncate">
                                                {{ $variantSku ?: 'Sin SKU' }}</p>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-center font-semibold {{ $isOutOfStock ? 'text-red-700' : ($isLowStock ? 'text-amber-700' : 'text-gray-900') }}">
                                            {{ $variantStock }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-700">{{ $variantMinStock }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($isOutOfStock)
                                                <span
                                                    class="inline-flex bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">Agotado</span>
                                            @elseif ($isLowStock)
                                                <span
                                                    class="inline-flex bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-medium">Bajo
                                                    stock</span>
                                            @elseif ($product->active)
                                                <span
                                                    class="inline-flex bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Activo</span>
                                            @else
                                                <span
                                                    class="inline-flex bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">Oculto</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @if ($variant)
                                                    <a href="{{ route('admin.inventario.kardex', $variant) }}"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                                                        title="Kardex" aria-label="Kardex de {{ $product->name }}">
                                                        <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                                    </a>
                                                @endif
                                                <button type="button"
                                                    class="open-stock-modal inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition"
                                                    data-product-id="{{ $product->id }}"
                                                    data-variant-id="{{ $variant?->id }}"
                                                    data-product-name="{{ $product->name }}"
                                                    data-variant-name="{{ $variantName }}"
                                                    data-current-stock="{{ $variantStock }}" title="Gestionar stock"
                                                    aria-label="Gestionar stock de {{ $product->name }}">
                                                    <x-heroicon-o-archive-box class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                            No hay productos para este negocio o búsqueda. Crea un producto o cambia el filtro.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="p-4 border-t">{{ $products->links() }}</div>
                    @endif
                </section>

                <aside class="{{ $panelClass }}">
                    <div class="px-4 sm:px-5 py-4 border-b border-gray-200/70 space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Movimientos</h3>
                            <p class="text-xs text-gray-400 mt-1">Entradas, salidas y ajustes recientes.</p>
                        </div>
                        <form method="GET" action="{{ route('admin.inventario.index') }}"
                            class="grid grid-cols-1 sm:grid-cols-3 2xl:grid-cols-1 gap-2">
                            <input type="hidden" name="catalog_type_id" value="{{ $selectedTypeId ?: '' }}">
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <select name="movement_type"
                                class="rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-xs text-gray-700">
                                <option value="">Todos</option>
                                <option value="in" {{ $movementType === 'in' ? 'selected' : '' }}>Entradas</option>
                                <option value="out" {{ $movementType === 'out' ? 'selected' : '' }}>Salidas</option>
                                <option value="adjust" {{ $movementType === 'adjust' ? 'selected' : '' }}>Ajustes</option>
                            </select>
                            <input type="date" name="movement_date_from" value="{{ $movementDateFrom }}"
                                class="rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-xs text-gray-700">
                            <input type="date" name="movement_date_to" value="{{ $movementDateTo }}"
                                class="rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-xs text-gray-700">
                            <button
                                class="sm:col-span-3 2xl:col-span-1 bg-gray-100 text-gray-700 rounded-lg py-2.5 text-xs font-semibold hover:bg-gray-200 transition">Filtrar
                                movimientos</button>
                        </form>
                    </div>
                    <div class="max-h-[620px] overflow-y-auto">
                        @forelse($recentMovements as $movement)
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 break-words">
                                            {{ $movement->variant->item->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 break-words">{{ $movement->variant->name ?? '-' }}</p>
                                    </div>
                                    <div class="flex gap-1 shrink-0">
                                        <a href="{{ route('admin.inventario.movements.edit', $movement) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100 transition"
                                            title="Editar movimiento" aria-label="Editar movimiento">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.inventario.movements.destroy', $movement) }}"
                                            onsubmit="return confirm('¿Anular este movimiento y registrar una reversa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition"
                                                title="Eliminar movimiento" aria-label="Eliminar movimiento">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p
                                    class="text-xs mt-2 text-gray-500 {{ $movement->voided_at ? 'line-through text-gray-400' : '' }}">
                                    <span class="font-semibold text-gray-700">{{ strtoupper($movement->type) }}</span>
                                    · Cant: {{ $movement->quantity }}
                                    · {{ $movement->stock_before ?? 0 }} -> {{ $movement->stock_after ?? 0 }}
                                    @if ($movement->voided_at)
                                        · ANULADO
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $movement->created_at?->format('d/m/Y H:i') }} ·
                                    {{ $movement->user->name ?? 'Sistema' }}</p>
                                @if ($movement->reason || $movement->reference)
                                    <p class="text-xs text-gray-400 mt-1">{{ $movement->reason ?: '-' }} ·
                                        {{ $movement->reference ?: '-' }}</p>
                                @endif
                                @if ($movement->location || $movement->fromLocation || $movement->toLocation)
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $movement->location?->name ?? ($movement->fromLocation?->name ?? '-') . '->' . ($movement->toLocation?->name ?? '-') }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-10 text-center text-gray-400 text-sm">Sin movimientos.</div>
                        @endforelse
                    </div>
                    @if ($recentMovements->hasPages())
                        <div class="p-3 border-t border-gray-100">{{ $recentMovements->links() }}</div>
                    @endif
                </aside>
            </div>

            <div id="stockModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
                <div class="absolute inset-0 bg-gray-900/50" data-stock-close></div>
                <div
                    class="relative w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden max-h-[92vh] overflow-y-auto">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Gestionar stock</p>
                            <h3 id="stockModalProduct" class="text-lg font-bold text-gray-900 truncate">Producto</h3>
                            <p id="stockModalVariant" class="text-sm text-gray-500 truncate">Presentación</p>
                        </div>
                        <button type="button"
                            class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition" data-stock-close
                            aria-label="Cerrar">
                            ×
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.inventario.movements.store') }}" class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="catalog_item_id" id="stockCatalogItemId">
                        <input type="hidden" name="catalog_item_variant_id" id="stockCatalogVariantId">

                        <div class="rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-500">Stock actual</span>
                            <span id="stockModalCurrent" class="text-lg font-bold text-gray-900">0</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Movimiento</label>
                            <select name="type"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <option value="in">Entrada de stock</option>
                                <option value="out">Salida de stock</option>
                                <option value="adjust">Ajustar stock exacto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <select name="inventory_location_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                                <option value="">Solo stock global</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                <input type="number" name="quantity" id="stockQuantity" min="1" value="1"
                                    required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Costo unitario</label>
                                <input type="number" name="unit_cost" min="0" step="0.01"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    placeholder="Costo promedio" {{ $canViewCosts ? '' : 'disabled' }}>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                                <input type="text" name="reason" maxlength="255"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    placeholder="Ej: compra, conteo">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                                <input type="text" name="reference" maxlength="255"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    placeholder="Ej: factura #001">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                                <input type="text" name="batch_number" maxlength="255"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    placeholder="Opcional">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vencimiento</label>
                                <input type="date" name="expires_at"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nota opcional</label>
                            <input type="text" name="notes" maxlength="1000"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                placeholder="Detalle del movimiento">
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                            <button type="button"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-sm font-semibold"
                                data-stock-close>
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition text-sm font-semibold">
                                Guardar movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endunless
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('stockModal');
            if (!modal) return;

            const productName = document.getElementById('stockModalProduct');
            const variantName = document.getElementById('stockModalVariant');
            const currentStock = document.getElementById('stockModalCurrent');
            const productInput = document.getElementById('stockCatalogItemId');
            const variantInput = document.getElementById('stockCatalogVariantId');
            const quantityInput = document.getElementById('stockQuantity');

            function openModal(button) {
                const variantId = button.dataset.variantId || '';

                productName.textContent = button.dataset.productName || 'Producto';
                variantName.textContent = button.dataset.variantName || 'Producto base';
                currentStock.textContent = button.dataset.currentStock || '0';
                productInput.value = variantId ? '' : (button.dataset.productId || '');
                variantInput.value = variantId;
                quantityInput.value = 1;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                quantityInput.focus();
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.querySelectorAll('.open-stock-modal').forEach((button) => {
                button.addEventListener('click', () => openModal(button));
            });

            modal.querySelectorAll('[data-stock-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
