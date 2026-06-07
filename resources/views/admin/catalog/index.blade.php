@extends('layouts.admin')

@section('title', 'Catálogo')

@section('content')
    @php
        $empresa = \App\Models\Empresa::query()->first();
        $sections = collect();

        if ($empresa) {
            $sections = \App\Models\CatalogType::query()
                ->where('empresa_id', $empresa->id)
                ->withCount(['categories', 'items'])
                ->ordered()
                ->get();
        }

        $statusBadge = fn($active) => $active
            ? 'bg-green-100 text-green-700 border-green-200'
            : 'bg-gray-100 text-gray-500 border-gray-200';

        $isProductBusiness = fn($section) => ($section->business_model ?? 'services') === \App\Models\CatalogType::BUSINESS_MODEL_PRODUCTS;
        $businessModelLabel = fn($section) => $isProductBusiness($section) ? 'Productos' : 'Servicios';
    @endphp

    <div class="container mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-squares-2x2 class="w-8 h-8 text-gray-800" />
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catálogo por Secciones</h2>
                </div>
                <p class="text-gray-500 text-sm mt-1">Entra a un negocio para crear sus categorías, servicios o productos, y
                    presentaciones.</p>
            </div>
            <a href="{{ route('admin.catalog-types.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition"
                title="Nueva sección" aria-label="Nueva sección">
                <x-heroicon-o-plus class="w-5 h-5" />
            </a>
        </div>

        <section class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div
                class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-gray-800">Secciones del negocio</h3>
                    <p class="text-xs text-gray-400 mt-1">Cada sección define si trabaja con servicios o productos.</p>
                </div>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @forelse($sections as $section)
                    <article class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="font-semibold text-gray-800 break-words">{{ $section->name }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5 break-words">{{ $section->slug ?: 'Sin slug' }}</p>
                            </div>
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full border text-xs font-medium shrink-0 {{ $statusBadge($section->active) }}">
                                {{ $section->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 mt-3 break-words">
                            {{ $section->description ?: 'Sin descripción adicional' }}
                        </p>

                        <div class="mt-3">
                            <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                {{ $businessModelLabel($section) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">Categorías</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ $section->categories_count }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-3 py-2">
                                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold">{{ $businessModelLabel($section) }}</p>
                                <p class="text-gray-800 font-semibold mt-1">{{ $section->items_count }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.catalog-types.show', $section) }}"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition"
                                title="Ingresar al negocio" aria-label="Ingresar al negocio">
                                <x-heroicon-o-arrow-right-circle class="w-5 h-5" />
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="p-8 text-center">
                        <x-heroicon-o-squares-2x2 class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                        <p class="font-semibold text-gray-700">Todavía no hay secciones.</p>
                        <p class="text-sm text-gray-400 mt-1 mb-5">Empieza creando una sección como Carwash, Bar Cafetería, Productos o Servicios.</p>
                        <a href="{{ route('admin.catalog-types.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition"
                            title="Nueva sección" aria-label="Nueva sección">
                            <x-heroicon-o-plus class="w-5 h-5" />
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="hidden md:block overflow-x-hidden">
                <table class="w-full table-fixed text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="w-[20%] px-4 py-3 text-center">Sección</th>
                            <th class="w-[24%] px-4 py-3 text-center">Descripción</th>
                            <th class="w-[12%] px-4 py-3 text-center">Modelo</th>
                            <th class="w-[12%] px-4 py-3 text-center">Categorías</th>
                            <th class="w-[14%] px-4 py-3 text-center">Items</th>
                            <th class="w-[12%] px-4 py-3 text-center">Estado</th>
                            <th class="w-[10%] px-4 py-3 text-center">Acci?nes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sections as $section)
                            <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-center">
                                    <p class="font-semibold text-gray-800 truncate">{{ $section->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $section->slug ?: 'Sin slug' }}</p>
                                </td>
                            <td class="px-4 py-4 text-gray-500 text-center">
                                    {{ \Illuminate\Support\Str::limit($section->description, 80) ?: 'Sin descripción adicional' }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                        {{ $businessModelLabel($section) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-semibold text-gray-800">{{ $section->categories_count }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-semibold text-gray-800">{{ $section->items_count }}</span>
                                </td>
                            <td class="px-4 py-4 text-center">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full border text-xs font-medium {{ $statusBadge($section->active) }}">
                                        {{ $section->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('admin.catalog-types.show', $section) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition"
                                            title="Ingresar al negocio" aria-label="Ingresar al negocio">
                                            <x-heroicon-o-arrow-right-circle class="w-5 h-5" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="max-w-md mx-auto">
                                        <x-heroicon-o-squares-2x2 class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                                        <p class="font-semibold text-gray-700">Todavía no hay secciones.</p>
                                        <p class="text-sm text-gray-400 mt-1 mb-5">Empieza creando una sección como Carwash,
                                            Bar Cafetería, Productos o Servicios.</p>
                                        <a href="{{ route('admin.catalog-types.create') }}"
                                            class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition"
                                            title="Nueva sección" aria-label="Nueva sección">
                                            <x-heroicon-o-plus class="w-5 h-5" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
