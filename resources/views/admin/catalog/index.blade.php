@extends('layouts.admin')

@section('title', 'Catalogo')

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
@endphp

<div class="container mx-auto px-4 sm:px-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 uppercase tracking-wide mb-3">
                <span>Admin</span>
                <span>/</span>
                <span class="text-gray-600 font-semibold">Catalogo</span>
            </div>
            <div class="flex items-center gap-2">
                <x-heroicon-o-squares-2x2 class="w-8 h-8 text-gray-800" />
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Catalogo por Secciones</h2>
            </div>
            <p class="text-gray-500 text-sm mt-1">Entra a un negocio para crear sus categorias, productos o servicios, y presentaciones.</p>
        </div>
        <a href="{{ route('admin.catalog-types.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition">
            + Nueva Seccion
        </a>
    </div>

        <section class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-gray-800">Secciones del negocio</h3>
                    <p class="text-xs text-gray-400 mt-1">Cada seccion puede tener sus propias categorias y productos.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left">Seccion</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Descripcion</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Categorias</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Productos/Servicios</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Estado</th>
                            <th class="px-4 sm:px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sections as $section)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4">
                                    <p class="font-semibold text-gray-800">{{ $section->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $section->slug ?: 'Sin slug' }}</p>
                                </td>
                                <td class="px-4 sm:px-6 py-4 text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($section->description, 80) ?: 'Sin descripcion adicional' }}
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="font-semibold text-gray-800">{{ $section->categories_count }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="font-semibold text-gray-800">{{ $section->items_count }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full border text-xs font-medium {{ $statusBadge($section->active) }}">
                                        {{ $section->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.catalog-types.show', $section) }}" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-700 transition">
                                            Ingresar al negocio
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="max-w-md mx-auto">
                                        <x-heroicon-o-squares-2x2 class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                                        <p class="font-semibold text-gray-700">Todavia no hay secciones.</p>
                                        <p class="text-sm text-gray-400 mt-1 mb-5">Empieza creando una seccion como Carwash, Bar Cafeteria, Productos o Servicios.</p>
                                        <a href="{{ route('admin.catalog-types.create') }}" class="inline-flex rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition">
                                            + Nueva Seccion
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
