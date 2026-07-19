@extends('layouts.admin')

@section('title', 'Inventario')

@section('content')
    @php
        $hasProductTypes = $productTypes->isNotEmpty();
    @endphp

    <div class="container mx-auto px-4 sm:px-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-archive-box class="w-8 h-8 text-gray-800" />
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Inventario</h2>
                </div>
            </div>
            @if ($hasProductTypes)
                <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2">
                    <form method="GET" action="{{ route('admin.inventario.index') }}"
                        class="w-full lg:w-auto flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:w-72">
                            <x-heroicon-o-magnifying-glass
                                class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input type="search" name="q" value="{{ request('q') }}"
                                class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700"
                                placeholder="Buscar producto, negocio, categoría, presentación o SKU...">
                        </div>
                        <select name="catalog_type_id"
                            class="w-full sm:w-56 rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-700"
                            onchange="this.form.submit()">
                            <option value="">Todos los negocios</option>
                            @foreach ($productTypes as $type)
                                <option value="{{ $type->id }}" {{ $selectedTypeId === $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.catalog-items.create', array_filter(['inventory' => 1, 'catalog_type_id' => $selectedTypeId ?: null])) }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Nuevo producto" aria-label="Nuevo producto">
                        <x-heroicon-o-cube class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.locations') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Ubicaciones" aria-label="Ubicaciones">
                        <x-heroicon-o-building-storefront class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.suppliers') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Proveedores" aria-label="Proveedores">
                        <x-heroicon-o-users class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.purchases') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Compras" aria-label="Compras">
                        <x-heroicon-o-shopping-bag class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.transfers') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Transferencias" aria-label="Transferencias">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.returns') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Devoluciones" aria-label="Devoluciones">
                        <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.counts') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Conteo físico" aria-label="Conteo físico">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.reports') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Reportes" aria-label="Reportes">
                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.periods') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Cierres" aria-label="Cierres">
                        <x-heroicon-o-lock-closed class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.import') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Importar CSV" aria-label="Importar CSV">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.export', array_filter(['catalog_type_id' => $selectedTypeId ?: null])) }}"
                        class="inline-flex items-center justify-center bg-white text-gray-800 border border-gray-200 w-11 h-11 rounded-lg hover:bg-gray-50 transition"
                        title="Exportar CSV" aria-label="Exportar CSV">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    </a>
                    <a href="{{ route('admin.inventario.create') }}"
                        class="inline-flex items-center justify-center bg-gray-900 text-white w-11 h-11 rounded-lg hover:bg-gray-700 transition"
                        title="Nuevo movimiento" aria-label="Nuevo movimiento">
                        <x-heroicon-o-plus class="w-5 h-5" />
                    </a>
                </div>
            @endif
        </div>

        @unless ($hasProductTypes)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 sm:p-10 text-center max-w-3xl mx-auto">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-700 flex items-center justify-center mx-auto mb-4">
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
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-2 mb-6 overflow-x-auto">
                <div class="flex items-center gap-2 min-w-max">
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

            {{-- <div
                class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 mb-6 flex flex-col sm:flex-row sm:items-center gap-3 text-sm text-blue-800">
                <div class="shrink-0">
                    <x-heroicon-o-information-circle class="w-5 h-5" />
                </div>
                <div>
                    <p class="font-semibold">Inventario trabaja solo con productos.</p>
                    <p class="text-blue-700 mt-0.5">Por ahora registra entradas, salidas y ajustes manuales. Proveedores y
                        compras se agregarán después sobre esta misma base.</p>
                </div>
            </div> --}}

            <div class="grid grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Presentaciones</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $inventoryStats['variants'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Unidades</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $inventoryStats['units'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Valor</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $canViewCosts ? '$' . number_format($inventoryStats['value'], 2) : 'Restringido' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Agotados</p>
                    <p class="text-2xl font-bold text-red-700 mt-2">{{ $inventoryStats['out'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Bajo stock</p>
                    <p class="text-2xl font-bold text-amber-700 mt-2">{{ $inventoryStats['low'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Sin costo</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">{{ $inventoryStats['no_cost'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Stock actual de productos</h3>
                    </div>

                    <div class="md:hidden space-y-3 p-4">
                        @forelse($products as $product)
                            @php
                                $variant = $product->variants->first();
                                $variantName = $variant ? $variant->name : 'Producto base';
                                $variantSku = $variant ? $variant->sku : null;
                                $variantStock = $variant ? (int) ($variant->stock ?? 0) : 0;
                                $variantMinStock = $variant ? (int) ($variant->min_stock ?? 0) : 0;
                                $isOutOfStock = $variantStock <= 0;
                                $isLowStock = !$isOutOfStock && $variantMinStock > 0 && $variantStock <= $variantMinStock;
                            @endphp
                            <div class="rounded-xl border border-gray-100 p-4 space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 break-words">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400 break-words">{{ $product->type->name ?? '-' }} ·
                                            {{ $product->category->name ?? 'Sin categoría' }}</p>
                                    </div>
                                    <span
                                        class="shrink-0 {{ $isOutOfStock ? 'bg-red-100 text-red-700' : ($isLowStock ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }} px-2.5 py-1 rounded-full text-xs font-semibold">
                                        Stock: {{ $variantStock }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Presentación</p>
                                        <p class="text-gray-700 break-words">{{ $variantName }}</p>
                                    </div>
                                    <div>
                                        <p class="font-mono text-xs text-gray-700 break-words">{{ $variantSku ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Mínimo</p>
                                        <p class="font-semibold text-gray-800">{{ $variantMinStock }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Estado</p>
                                        @if ($isOutOfStock)
                                            <span
                                                class="inline-flex bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium">Agotado</span>
                                        @elseif ($isLowStock)
                                            <span
                                                class="inline-flex bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-medium">Bajo stock</span>
                                        @elseif ($product->active)
                                            <span
                                                class="inline-flex bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-medium">Activo</span>
                                        @else
                                            <span
                                                class="inline-flex bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium">Oculto</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-gray-400 font-semibold">Precio</p>
                                        <p class="font-semibold text-gray-800">
                                            ${{ number_format($product->base_price ?? 0, 2) }}</p>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button"
                                        class="open-stock-modal inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-semibold"
                                        data-product-id="{{ $product->id }}" data-variant-id="{{ $variant?->id }}"
                                        data-product-name="{{ $product->name }}" data-variant-name="{{ $variantName }}"
                                        data-current-stock="{{ $variantStock }}" title="Gestionar stock"
                                        aria-label="Gestionar stock de {{ $product->name }}">
                                        <x-heroicon-o-archive-box class="w-4 h-4" />
                                        Gestionar stock
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400">No hay productos para este negocio o búsqueda. Crea
                                un producto o cambia el filtro.</div>
                        @endforelse
                    </div>

                    <div class="hidden md:block overflow-x-hidden">
                        <table class="w-full table-fixed text-sm text-left">
                            <thead class="bg-gray-50 border-b text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-3 py-3 text-center w-[20%]">Producto</th>
                                    <th class="px-3 py-3 text-center w-[14%]">Negocio</th>
                                    <th class="px-3 py-3 text-center w-[14%]">Categoría</th>
                                    <th class="px-3 py-3 text-center w-[16%]">Presentación</th>
                                    <th class="px-3 py-3 text-center w-[8%]">Stock</th>
                                    <th class="px-3 py-3 text-center w-[8%]">Mín.</th>
                                    <th class="px-3 py-3 text-center w-[10%]">Estado</th>
                                    <th class="px-3 py-3 text-center w-[10%]">Acción</th>
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
                                        $isLowStock = !$isOutOfStock && $variantMinStock > 0 && $variantStock <= $variantMinStock;
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3 text-center">
                                            <p class="font-medium text-gray-800 truncate">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-400 truncate">
                                                {{ $product->description ?: 'Sin descripción' }}</p>
                                        </td>
                                        <td class="px-3 py-3 text-center text-gray-700 truncate">
                                            {{ $product->type->name ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center text-gray-700 truncate">
                                            {{ $product->category->name ?? 'Sin categoría' }}</td>
                                        <td class="px-3 py-3 text-center">
                                            <p class="text-gray-700 truncate">{{ $variantName }}</p>
                                            <p class="text-gray-500 font-mono text-xs truncate">{{ $variantSku ?: 'Sin SKU' }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-3 text-center font-semibold {{ $isOutOfStock ? 'text-red-700' : ($isLowStock ? 'text-amber-700' : 'text-gray-800') }}">{{ $variantStock }}</td>
                                        <td class="px-3 py-3 text-center text-gray-700">{{ $variantMinStock }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($isOutOfStock)
                                                <span
                                                    class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">Agotado</span>
                                            @elseif ($isLowStock)
                                                <span
                                                    class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-medium">Bajo stock</span>
                                            @elseif ($product->active)
                                                <span
                                                    class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">Activo</span>
                                            @else
                                                <span
                                                    class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">Oculto</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                @if($variant)
                                                    <a href="{{ route('admin.inventario.kardex', $variant) }}"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                                                        title="Kardex" aria-label="Kardex de {{ $product->name }}">
                                                        <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                                    </a>
                                                @endif
                                                <button type="button"
                                                    class="open-stock-modal inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition"
                                                    data-product-id="{{ $product->id }}" data-variant-id="{{ $variant?->id }}"
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
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">No hay productos para
                                            este negocio o búsqueda. Crea un producto o cambia el filtro.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="p-4 border-t">{{ $products->links() }}</div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 space-y-3">
                        <h3 class="font-semibold text-gray-800">Movimientos</h3>
                        <form method="GET" action="{{ route('admin.inventario.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <input type="hidden" name="catalog_type_id" value="{{ $selectedTypeId ?: '' }}">
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <select name="movement_type" class="rounded-lg border border-gray-200 bg-white py-2 px-3 text-xs text-gray-700">
                                <option value="">Todos</option>
                                <option value="in" {{ $movementType === 'in' ? 'selected' : '' }}>Entradas</option>
                                <option value="out" {{ $movementType === 'out' ? 'selected' : '' }}>Salidas</option>
                                <option value="adjust" {{ $movementType === 'adjust' ? 'selected' : '' }}>Ajustes</option>
                            </select>
                            <input type="date" name="movement_date_from" value="{{ $movementDateFrom }}" class="rounded-lg border border-gray-200 bg-white py-2 px-3 text-xs text-gray-700">
                            <input type="date" name="movement_date_to" value="{{ $movementDateTo }}" class="rounded-lg border border-gray-200 bg-white py-2 px-3 text-xs text-gray-700">
                            <button class="sm:col-span-3 bg-gray-100 text-gray-700 rounded-lg py-2 text-xs font-semibold hover:bg-gray-200 transition">Filtrar movimientos</button>
                        </form>
                    </div>
                    <div class="max-h-[560px] overflow-y-auto">
                        @forelse($recentMovements as $movement)
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 break-words">
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
                                <p class="text-xs mt-2 text-center {{ $movement->voided_at ? 'line-through text-gray-400' : '' }}">
                                    <span class="font-medium">{{ strtoupper($movement->type) }}</span>
                                    · Cant: {{ $movement->quantity }}
                                    · {{ $movement->stock_before ?? 0 }} → {{ $movement->stock_after ?? 0 }}
                                    @if($movement->voided_at)
                                        · ANULADO
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-1 text-center">
                                    {{ $movement->created_at?->format('d/m/Y H:i') }} ·
                                    {{ $movement->user->name ?? 'Sistema' }}</p>
                                @if($movement->reason || $movement->reference)
                                    <p class="text-xs text-gray-400 mt-1 text-center">
                                        {{ $movement->reason ?: '-' }} · {{ $movement->reference ?: '-' }}
                                    </p>
                                @endif
                                @if($movement->location || $movement->fromLocation || $movement->toLocation)
                                    <p class="text-xs text-gray-400 mt-1 text-center">
                                        {{ $movement->location?->name ?? (($movement->fromLocation?->name ?? '-') . ' -> ' . ($movement->toLocation?->name ?? '-')) }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">Sin movimientos.</div>
                        @endforelse
                    </div>
                    @if($recentMovements->hasPages())
                        <div class="p-3 border-t border-gray-100">{{ $recentMovements->links() }}</div>
                    @endif
                </div>
            </div>

            <div id="stockModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
                <div class="absolute inset-0 bg-gray-900/50" data-stock-close></div>
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs uppercase tracking-wide text-gray-400 font-semibold">Gestionar stock</p>
                            <h3 id="stockModalProduct" class="text-lg font-bold text-gray-800 truncate">Producto</h3>
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

                        <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-500">Stock actual</span>
                            <span id="stockModalCurrent" class="text-lg font-bold text-gray-800">0</span>
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
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" name="quantity" id="stockQuantity" min="1" value="1" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Costo unitario</label>
                            <input type="number" name="unit_cost" min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                placeholder="Usa costo promedio actual" {{ $canViewCosts ? '' : 'disabled' }}>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                                <input type="text" name="reason" maxlength="255"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300"
                                    placeholder="Ej: compra, conteo, pérdida">
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
                                placeholder="Ej: compra, pérdida, corrección de conteo">
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
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
