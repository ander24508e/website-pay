@extends('layouts.admin')

@section('title', 'Detalle de Sección')

@section('content')
    @php
        $isProductBusiness = ($catalogType->business_model ?? \App\Models\CatalogType::BUSINESS_MODEL_SERVICES) === \App\Models\CatalogType::BUSINESS_MODEL_PRODUCTS;
        $businessModelLabel = $isProductBusiness ? 'Productos' : 'Servicios';
        $itemSingular = $isProductBusiness ? 'producto' : 'servicio';
        $itemSingularTitle = $isProductBusiness ? 'Producto' : 'Servicio';
        $itemPlural = $isProductBusiness ? 'productos' : 'servicios';
        $itemPluralTitle = $isProductBusiness ? 'Productos' : 'Servicios';
        $itemShortLabel = $isProductBusiness ? 'Prod.' : 'Serv.';
        $primaryButtonClass = 'inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-medium text-center shrink-0';
        $secondaryButtonClass = 'inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium text-center shrink-0';
    @endphp

    <div class="mx-auto w-full max-w-6xl overflow-x-hidden px-3 pb-4 sm:px-6">
        <div class="flex items-start gap-3 mb-4">
            <a href="{{ route('admin.catalog.index') }}"
                class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800 shrink-0">
                <span aria-hidden="true">&larr;</span>
            </a>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $catalogType->name }}</h2>
                <p class="text-gray-400 text-sm">Configura esta sección y su contenido en la web.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[320px_minmax(0,1fr)] gap-4">
            <aside class="min-w-0">
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                    <div class="flex flex-wrap gap-3 mb-5">
                        <h3 class="text-lg font-bold text-gray-800">Información del Negocio</h3>
                    </div>

                    <div class="space-y-4 mb-6 min-w-0">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Nombre</p>
                            <p class="font-semibold text-gray-800 break-words">{{ $catalogType->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Slug</p>
                            <p class="text-gray-700 font-mono text-sm break-all">{{ $catalogType->slug ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Descripción</p>
                            <p class="text-gray-700 break-words">{{ $catalogType->description ?: 'Sin descripción.' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Modelo del negocio</p>
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $businessModelLabel }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">Categorías</p>
                                <p class="text-gray-700 font-semibold">{{ $catalogType->categories_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $itemPluralTitle }}</p>
                                <p class="text-gray-700 font-semibold">{{ $catalogType->items_count }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Estado</p>
                            @if ($catalogType->active)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-flex">Activo</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium inline-flex">Oculto</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 pt-4 border-t border-gray-100 sm:flex-row">
                        <a href="{{ route('admin.catalog-types.edit', $catalogType) }}"
                            class="w-full bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition font-medium text-sm text-center sm:w-auto">
                            Editar
                        </a>
                        <form action="{{ route('admin.catalog-types.destroy', $catalogType) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta sección?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-full bg-red-50 text-red-600 px-5 py-2.5 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200 sm:w-auto">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <section class="min-w-0 flex flex-col gap-4">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800">Categorías de esta sección</h3>
                            <p class="text-sm text-gray-400">Organiza tus {{ $itemPlural }} por categoría.</p>
                        </div>
                        <div class="flex flex-col items-stretch gap-2 shrink-0 sm:flex-row sm:items-center">
                            <a href="{{ route('admin.catalog-categories.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                class="{{ $primaryButtonClass }}">
                                + Nueva Categoría
                            </a>
                            <a href="{{ route('admin.catalog-categories.index', ['catalog_type_id' => $catalogType->id]) }}"
                                class="{{ $secondaryButtonClass }}">
                                Mostrar Todo
                            </a>
                        </div>
                    </div>

                    @if ($catalogType->categories->isEmpty())
                        <div class="m-4 sm:m-5 rounded-lg border border-dashed border-gray-200 bg-gray-50 p-5 text-sm text-gray-500">
                            Crea una categoría para organizar tus {{ $itemPlural }}.
                        </div>
                    @else
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach ($catalogType->categories as $category)
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 break-words">{{ $category->name }}</p>
                                            <p class="text-xs text-gray-400 mt-1 break-words">{{ $category->slug ?: 'Sin slug' }}</p>
                                        </div>
                                        @if ($category->active)
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Activo</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Oculto</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3 break-words">{{ $category->description ?: 'Sin descripción' }}</p>
                                    <div class="grid grid-cols-1 gap-3 text-sm mt-3">
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">{{ $itemPluralTitle }}</p>
                                            <p class="font-semibold text-gray-700">{{ $category->items_count }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-3">
                                        <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $category->catalog_type_id, 'catalog_category_id' => $category->id]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                            title="Ver {{ $itemPlural }}" aria-label="Ver {{ $itemPlural }}">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-categories.edit', ['catalogCategory' => $category, 'return_to_type' => 1]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                            title="Editar categoría" aria-label="Editar categoría">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-categories.destroy', $category) }}"
                                            onsubmit="return confirm('¿Eliminar esta categoría? Los {{ $itemPlural }} quedarán sin categoría.');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to_type" value="1">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                                title="Eliminar categoría" aria-label="Eliminar categoría">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-y-auto overflow-x-hidden max-h-[320px] flex-1">
                            <table class="w-full table-fixed text-sm text-left">
                                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                    <tr>
                                        <th class="w-[28%] px-3 py-2.5 text-gray-600 font-semibold text-center">Categoría</th>
                                        <th class="w-[18%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden sm:table-cell">Slug</th>
                                        <th class="w-[16%] px-3 py-2.5 text-gray-600 font-semibold text-center">{{ $itemShortLabel }}</th>
                                        <th class="w-[12%] px-3 py-2.5 text-gray-600 font-semibold text-center">Estado</th>
                                        <th class="w-[24%] px-3 py-2.5 text-gray-600 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($catalogType->categories as $category)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2.5 align-top text-center">
                                                <p class="font-semibold text-gray-800 truncate">{{ $category->name }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5 leading-tight line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($category->description, 55) ?: 'Sin descripción' }}
                                                </p>
                                            </td>
                                            <td class="px-3 py-2.5 text-gray-600 text-center truncate hidden sm:table-cell">{{ $category->slug ?: '-' }}</td>
                                            <td class="px-3 py-2.5 text-gray-700 text-center">{{ $category->items_count }}</td>
                                            <td class="px-3 py-2.5 text-center">
                                                @if ($category->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium">Activo</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium">Oculto</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $category->catalog_type_id, 'catalog_category_id' => $category->id]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                                        title="Ver {{ $itemPlural }}" aria-label="Ver {{ $itemPlural }}">
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-categories.edit', ['catalogCategory' => $category, 'return_to_type' => 1]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                                        title="Editar categoría" aria-label="Editar categoría">
                                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.catalog-categories.destroy', $category) }}"
                                                        onsubmit="return confirm('¿Eliminar esta categoría? Los {{ $itemPlural }} quedarán sin categoría.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_to_type" value="1">
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                                            title="Eliminar categoría" aria-label="Eliminar categoría">
                                                            <x-heroicon-o-trash class="w-4 h-4" />
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shrink-0">
                        <div class="min-w-0">
                            <h3 class="text-lg font-bold text-gray-800">{{ $itemPluralTitle }} dentro de {{ $catalogType->name }}</h3>
                            <p class="text-sm text-gray-400">Se muestran en la web y se usan en ventas.</p>
                        </div>
                        <div class="flex flex-col items-stretch gap-2 shrink-0 sm:flex-row sm:items-center">
                            <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                class="{{ $primaryButtonClass }}">
                                + Agregar {{ $itemSingularTitle }}
                            </a>
                            <a href="{{ route('admin.catalog-items.index', ['catalog_type_id' => $catalogType->id]) }}"
                                class="{{ $secondaryButtonClass }}">
                                Mostrar Todo
                            </a>
                        </div>
                    </div>

                    @if ($catalogType->items->isEmpty())
                        <div class="p-6 sm:p-8 text-center text-gray-500">
                            <p class="font-medium text-gray-700 mb-2">Aún no tienes {{ $itemPlural }} aquí.</p>
                            <p class="text-sm mb-5">Crea el primero para venderlo o mostrarlo en la web.</p>
                            <a href="{{ route('admin.catalog-items.create', ['catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                class="{{ $primaryButtonClass }}">
                                Crear {{ $itemSingularTitle }}
                            </a>
                        </div>
                    @else
                        <div class="md:hidden divide-y divide-gray-100">
                            @foreach ($catalogType->items as $item)
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-800 break-words">{{ $item->name }}</p>
                                            <p class="text-xs text-gray-400 mt-1 break-words">{{ $item->category?->name ?: 'Sin categoría' }}</p>
                                        </div>
                                        @if ($item->active)
                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Activo</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium shrink-0">Oculto</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3 break-words">{{ $item->description ?: 'Sin descripción' }}</p>
                                    <div class="grid grid-cols-2 gap-3 text-sm mt-3">
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Precio</p>
                                            <p class="font-semibold text-gray-700">{{ $item->base_price !== null ? '$' . number_format((float) $item->base_price, 2) : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 uppercase">Presentaciones</p>
                                            <p class="font-semibold text-gray-700">{{ $item->variants_count }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-3">
                                        <a href="{{ route('admin.catalog-items.show', ['catalogItem' => $item, 'return_to_type' => 1]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                            title="Ver {{ $itemSingular }}" aria-label="Ver {{ $itemSingular }}">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-items.edit', ['catalogItem' => $item, 'return_to_type' => 1]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                            title="Editar {{ $itemSingular }}" aria-label="Editar {{ $itemSingular }}">
                                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $item->id, 'catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
                                            title="Agregar presentación" aria-label="Agregar presentación">
                                            <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}"
                                            onsubmit="return confirm('¿Eliminar este {{ $itemSingular }}? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to_type" value="1">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                                title="Eliminar {{ $itemSingular }}" aria-label="Eliminar {{ $itemSingular }}">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-y-auto overflow-x-hidden max-h-[320px] flex-1">
                            <table class="w-full table-fixed text-sm text-left">
                                <thead class="bg-gray-50 border-b sticky top-0 z-10">
                                    <tr>
                                        <th class="w-[28%] px-3 py-2.5 text-gray-600 font-semibold text-center">{{ $itemSingularTitle }}</th>
                                        <th class="w-[18%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden sm:table-cell">Categoría</th>
                                        <th class="w-[13%] px-3 py-2.5 text-gray-600 font-semibold text-center">Precio</th>
                                        <th class="w-[10%] px-3 py-2.5 text-gray-600 font-semibold text-center hidden md:table-cell">Pres.</th>
                                        <th class="w-[11%] px-3 py-2.5 text-gray-600 font-semibold text-center">Estado</th>
                                        <th class="w-[20%] px-3 py-2.5 text-gray-600 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($catalogType->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2.5 align-top text-center">
                                                <p class="font-semibold text-gray-800 truncate">{{ $item->name }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5 leading-tight line-clamp-2">
                                                    {{ \Illuminate\Support\Str::limit($item->description, 55) ?: 'Sin descripción' }}
                                                </p>
                                            </td>
                                            <td class="px-3 py-2.5 text-gray-600 text-center truncate hidden sm:table-cell">{{ $item->category?->name ?: 'Sin categoría' }}</td>
                                            <td class="px-3 py-2.5 font-semibold text-gray-800 text-center whitespace-nowrap">
                                                {{ $item->base_price !== null ? '$' . number_format((float) $item->base_price, 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-gray-700 text-center hidden md:table-cell">{{ $item->variants_count }}</td>
                                            <td class="px-3 py-2.5 text-center">
                                                @if ($item->active)
                                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-medium">Activo</span>
                                                @else
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[11px] font-medium">Oculto</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('admin.catalog-items.show', ['catalogItem' => $item, 'return_to_type' => 1]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                                        title="Ver {{ $itemSingular }}" aria-label="Ver {{ $itemSingular }}">
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-items.edit', ['catalogItem' => $item, 'return_to_type' => 1]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 transition"
                                                        title="Editar {{ $itemSingular }}" aria-label="Editar {{ $itemSingular }}">
                                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.catalog-variants.create', ['catalog_item_id' => $item->id, 'catalog_type_id' => $catalogType->id, 'return_to_type' => 1]) }}"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
                                                        title="Agregar presentación" aria-label="Agregar presentación">
                                                        <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.catalog-items.destroy', $item) }}"
                                                        onsubmit="return confirm('¿Eliminar este {{ $itemSingular }}? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="return_to_type" value="1">
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition"
                                                            title="Eliminar {{ $itemSingular }}" aria-label="Eliminar {{ $itemSingular }}">
                                                            <x-heroicon-o-trash class="w-4 h-4" />
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
