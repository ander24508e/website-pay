@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
    @php
        $isProductContext =
            isset($selectedType) &&
            $selectedType &&
            ($selectedType->business_model ?? 'services') === \App\Models\CatalogType::BUSINESS_MODEL_PRODUCTS;
        $itemPlural =
            isset($selectedType) && $selectedType
                ? ($isProductContext
                    ? 'productos'
                    : 'servicios')
                : 'productos y servicios';
        $backUrl = isset($selectedType) && $selectedType
            ? route('admin.catalog-types.show', $selectedType)
            : route('admin.catalog.index');
    @endphp

    <div class="mx-auto w-full max-w-full overflow-x-hidden px-3 pb-4 sm:px-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <a href="{{ $backUrl }}"
                        class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                        <span aria-hidden="true">&larr;</span>
                    </a>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Categorías</h2>
                        <p class="text-gray-500 text-sm mt-1">
                            @if (isset($selectedType) && $selectedType)
                                Estás viendo las Categorías de la Sección {{ $selectedType->name }}.
                            @else
                                Organiza {{ $itemPlural }} dentro de cada sección.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.catalog-categories.create', array_filter(['catalog_type_id' => isset($selectedType) && $selectedType ? $selectedType->id : null, 'return_to_type' => isset($selectedType) && $selectedType ? 1 : null])) }}"
                class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-center">
                <x-heroicon-o-plus class="w-5 h-5" />
                <span>Nueva Categoría</span>
            </a>
        </div>

        <div class="mb-6 overflow-x-auto">
            <div
                class="inline-flex min-w-max items-center gap-1 rounded-full bg-white p-1 shadow-sm border border-gray-100">
                <a href="{{ route('admin.catalog-categories.index') }}"
                    class="px-5 py-2 rounded-full text-sm font-medium transition {{ request('catalog_type_id') ? 'text-gray-700 hover:bg-gray-50' : 'bg-gray-900 text-white shadow-sm' }}">
                    Todos
                </a>

                @foreach ($types as $type)
                    <a href="{{ route('admin.catalog-categories.index', ['catalog_type_id' => $type->id]) }}"
                        class="px-5 py-2 rounded-full text-sm font-medium transition {{ (string) request('catalog_type_id') === (string) $type->id ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50' }}">
                        {{ $type->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($categories as $category)
                    <article class="p-4 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 break-words">{{ $category->name }}</p>
                                <p class="mt-1 font-mono text-xs text-gray-400 break-all">{{ $category->slug ?: '-' }}</p>
                            </div>
                            @if ($category->active)
                                <span class="shrink-0 bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium">Activa</span>
                            @else
                                <span class="shrink-0 bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-medium">Oculta</span>
                            @endif
                        </div>

                        @if ($category->description)
                            <p class="text-sm text-gray-500 break-words">{{ \Illuminate\Support\Str::limit($category->description, 110) }}</p>
                        @endif

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Seccion</p>
                                <p class="mt-1 text-gray-800 break-words">{{ $category->type?->name ?? 'Sin seccion' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">{{ ucfirst($itemPlural) }}</p>
                                <p class="mt-1 font-semibold text-gray-800">{{ $category->items_count }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.catalog-categories.edit', $category) }}"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition"
                                title="Editar" aria-label="Editar">
                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                            </a>
                            <form method="POST" action="{{ route('admin.catalog-categories.destroy', $category) }}"
                                onsubmit="return confirm('¿Eliminar esta categoría universal?');">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition"
                                    title="Eliminar" aria-label="Eliminar">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center text-gray-400">No hay Categorias registradas</div>
                @endforelse
            </div>

            <div class="hidden md:block overflow-y-auto max-h-[70vh]">
                <table class="w-full table-fixed text-sm text-left">
                    <thead class="bg-gray-50 border-b sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Nombre</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Sección</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Slug</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Ítems</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Estado</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                    <p class="font-medium text-gray-800">{{ $category->name }}</p>
                                    @if ($category->description)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($category->description, 70) }}</p>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $category->type?->name ?? 'Sin sección' }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-500 font-mono text-xs text-center">
                                    {{ $category->slug ?: '-' }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 font-semibold text-gray-800 text-center">
                                    {{ $category->items_count }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                    @if ($category->active)
                                        <span
                                            class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Activa</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-600 px-2 sm:px-3 py-1 rounded-full text-xs font-medium">Oculta</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.catalog-categories.edit', $category) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:bg-yellow-50 transition"
                                            title="Editar" aria-label="Editar">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.catalog-categories.destroy', $category) }}"
                                            onsubmit="return confirm('¿Eliminar esta categoría universal?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition"
                                                title="Eliminar" aria-label="Eliminar">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-400">No hay Categorías Registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="p-4 border-t">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
