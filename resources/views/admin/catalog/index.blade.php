@extends('layouts.admin')

@section('title', 'Catalogo')

@section('content')
@php
    $empresa = \App\Models\Empresa::query()->first();
    $empresaId = $empresa?->id;

    $types = collect();
    $categories = collect();
    $items = collect();
    $variants = collect();

    if ($empresaId) {
        $types = \App\Models\CatalogType::query()
            ->where('empresa_id', $empresaId)
            ->ordered()
            ->limit(8)
            ->get();

        $categories = \App\Models\CatalogCategory::query()
            ->where('empresa_id', $empresaId)
            ->with('type')
            ->ordered()
            ->limit(8)
            ->get();

        $items = \App\Models\CatalogItem::query()
            ->where('empresa_id', $empresaId)
            ->with(['category'])
            ->ordered()
            ->limit(8)
            ->get();

        $variants = \App\Models\CatalogItemVariant::query()
            ->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId))
            ->with('item')
            ->ordered()
            ->limit(8)
            ->get();
    }

    $statusBadge = fn($active) => $active
        ? 'bg-green-100 text-green-700 border-green-200'
        : 'bg-gray-100 text-gray-500 border-gray-200';
@endphp

<div class="container mx-auto px-4 sm:px-6">
    <div class="mb-6">
        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-3">
            <span>Admin</span>
            <span>/</span>
            <span class="text-gray-600 font-semibold">Catalogo</span>
        </div>
        <div class="flex items-center gap-2">
            <x-heroicon-o-squares-2x2 class="w-8 h-8 text-gray-800" />
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catalogo</h2>
        </div>
        <p class="text-gray-500 text-sm mt-1">Visualizacion rapida de subnegocios, categorias, items y variantes.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 xl:gap-5">
        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Subnegocios</h3>
                <a href="{{ route('admin.catalog-types.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Nombre</th>
                            <th class="px-3 py-2 text-left">Descripcion</th>
                            <th class="px-3 py-2 text-left">Estado</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-500">{{ $type->id }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $type->name }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ \Illuminate\Support\Str::limit($type->description, 38, '...') ?: '-' }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-full border text-xs font-medium {{ $statusBadge($type->active) }}">
                                        {{ $type->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.catalog-types.edit', $type) }}" class="text-gray-500 hover:text-blue-600" title="Editar">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-types.destroy', $type) }}" onsubmit="return confirm('¿Eliminar subnegocio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-400">Sin subnegocios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Categorias Universales</h3>
                <a href="{{ route('admin.catalog-categories.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Nombre</th>
                            <th class="px-3 py-2 text-left">Subnegocio</th>
                            <th class="px-3 py-2 text-left">Estado</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-500">{{ $category->id }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $category->name }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $category->type?->name ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-full border text-xs font-medium {{ $statusBadge($category->active) }}">
                                        {{ $category->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.catalog-categories.edit', $category) }}" class="text-gray-500 hover:text-blue-600" title="Editar">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar categoria?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-400">Sin categorias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Items Universales</h3>
                <a href="{{ route('admin.catalog-items.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Nombre</th>
                            <th class="px-3 py-2 text-left">Categoria</th>
                            <th class="px-3 py-2 text-left">Precio</th>
                            <th class="px-3 py-2 text-left">Estado</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-500">{{ $item->id }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $item->name }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $item->category?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">${{ number_format((float) ($item->base_price ?? 0), 2) }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-full border text-xs font-medium {{ $statusBadge($item->active) }}">
                                        {{ $item->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.catalog-items.edit', $item) }}" class="text-gray-500 hover:text-blue-600" title="Editar">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}" onsubmit="return confirm('¿Eliminar item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-400">Sin items registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Variantes Universales</h3>
                <a href="{{ route('admin.catalog-variants.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Nombre</th>
                            <th class="px-3 py-2 text-left">Item Base</th>
                            <th class="px-3 py-2 text-left">Variaciones</th>
                            <th class="px-3 py-2 text-left">Estado</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variants as $variant)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-500">{{ $variant->id }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $variant->name }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $variant->item?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-500">
                                    {{ collect([$variant->presentation, $variant->specification])->filter()->implode(' / ') ?: '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex px-2 py-0.5 rounded-full border text-xs font-medium {{ $statusBadge($variant->active) }}">
                                        {{ $variant->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.catalog-variants.edit', $variant) }}" class="text-gray-500 hover:text-blue-600" title="Editar">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-variants.destroy', $variant) }}" onsubmit="return confirm('¿Eliminar variante?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600" title="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-400">Sin variantes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
