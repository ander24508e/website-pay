@extends('layouts.admin')

@section('title', 'Especificaciones de Vehículos')

@section('content')
    <div class="min-h-[calc(100vh-3rem)] space-y-5 overflow-x-hidden">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.vehiculos.index') }}"
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800"
                title="Volver" aria-label="Volver">
                <x-heroicon-o-arrow-left class="h-5 w-5" />
            </a>
            <div class="min-w-0">
                <h2 class="break-words text-xl font-bold text-gray-800 sm:text-2xl">Especificaciones de Vehículos</h2>
                <p class="mt-1 text-sm text-gray-500">Administra marcas, modelos y tipos de vehículo.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Revisa los campos marcados.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <section class="flex min-h-[420px] min-w-0 flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-4 sm:p-5">
                    <h3 class="font-bold text-gray-800">Marcas</h3>
                    <p class="mt-1 text-xs text-gray-400">Registra marcas reutilizables.</p>
                </div>

                <form method="POST" action="{{ route('admin.vehiculos.specifications.brands.store') }}"
                    class="space-y-3 border-b border-gray-100 p-4 sm:p-5">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Ej: Toyota" required>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="rounded border-gray-300" checked>
                            Activo
                        </label>
                        <button class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                            Crear marca
                        </button>
                    </div>
                </form>

                <div class="min-h-0 flex-1 overflow-auto">
                    <table class="min-w-[420px] w-full table-fixed text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="w-[54%] px-3 py-3 text-center">Marca</th>
                                <th class="w-[22%] px-3 py-3 text-center">Estado</th>
                                <th class="w-[24%] px-3 py-3 text-center">Acc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($brands as $brand)
                                @php($brandFormId = 'brand-update-' . $brand->id)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-3 text-center">
                                        <form id="{{ $brandFormId }}" method="POST"
                                            action="{{ route('admin.vehiculos.specifications.brands.update', $brand) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="active" value="0">
                                        </form>
                                        <input form="{{ $brandFormId }}" type="text" name="name" value="{{ $brand->name }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-800">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-50 text-gray-700" title="Activo">
                                            <input form="{{ $brandFormId }}" type="checkbox" name="active" value="1"
                                                class="sr-only peer" @checked($brand->active)>
                                            <x-heroicon-o-check-circle class="h-5 w-5 peer-checked:text-emerald-600" />
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button form="{{ $brandFormId }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100"
                                                title="Guardar" aria-label="Guardar">
                                                <x-heroicon-o-check class="h-4 w-4" />
                                            </button>
                                            <form method="POST" action="{{ route('admin.vehiculos.specifications.brands.destroy', $brand) }}"
                                                onsubmit="return confirm('¿Eliminar esta marca?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 disabled:opacity-30"
                                                    title="Eliminar" aria-label="Eliminar" @disabled($brand->vehicles_count || $brand->models_count)>
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">No hay marcas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="flex min-h-[420px] min-w-0 flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-4 sm:p-5">
                    <h3 class="font-bold text-gray-800">Modelos</h3>
                    <p class="mt-1 text-xs text-gray-400">Relaciona cada modelo con una marca.</p>
                </div>

                <form method="POST" action="{{ route('admin.vehiculos.specifications.models.store') }}"
                    class="space-y-3 border-b border-gray-100 p-4 sm:p-5">
                    @csrf
                    <select name="vehicle_brand_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" required>
                        <option value="">Selecciona una marca</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('vehicle_brand_id') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Ej: Grand Vitara, F-150" required>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="rounded border-gray-300" checked>
                            Activo
                        </label>
                        <button class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                            Crear modelo
                        </button>
                    </div>
                </form>

                <div class="min-h-0 flex-1 overflow-auto">
                    <table class="min-w-[520px] w-full table-fixed text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="w-[36%] px-3 py-3 text-center">Marca</th>
                                <th class="w-[34%] px-3 py-3 text-center">Modelo</th>
                                <th class="w-[14%] px-3 py-3 text-center">Estado</th>
                                <th class="w-[16%] px-3 py-3 text-center">Acc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $model)
                                @php($modelFormId = 'model-update-' . $model->id)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-3 text-center">
                                        <form id="{{ $modelFormId }}" method="POST"
                                            action="{{ route('admin.vehiculos.specifications.models.update', $model) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="active" value="0">
                                        </form>
                                        <select form="{{ $modelFormId }}" name="vehicle_brand_id"
                                            class="w-full rounded-lg border border-gray-200 px-2 py-2 text-center text-sm">
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" @selected($model->vehicle_brand_id == $brand->id)>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <input form="{{ $modelFormId }}" type="text" name="name" value="{{ $model->name }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-800">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-50 text-gray-700" title="Activo">
                                            <input form="{{ $modelFormId }}" type="checkbox" name="active" value="1"
                                                class="sr-only peer" @checked($model->active)>
                                            <x-heroicon-o-check-circle class="h-5 w-5 peer-checked:text-emerald-600" />
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button form="{{ $modelFormId }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100"
                                                title="Guardar" aria-label="Guardar">
                                                <x-heroicon-o-check class="h-4 w-4" />
                                            </button>
                                            <form method="POST" action="{{ route('admin.vehiculos.specifications.models.destroy', $model) }}"
                                                onsubmit="return confirm('¿Eliminar este modelo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 disabled:opacity-30"
                                                    title="Eliminar" aria-label="Eliminar" @disabled($model->vehicles_count)>
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">No hay modelos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="flex min-h-[420px] min-w-0 flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-4 sm:p-5">
                    <h3 class="font-bold text-gray-800">Tipos de vehículo</h3>
                    <p class="mt-1 text-xs text-gray-400">Define clases como sedán, SUV o camioneta.</p>
                </div>

                <form method="POST" action="{{ route('admin.vehiculos.specifications.types.store') }}"
                    class="space-y-3 border-b border-gray-100 p-4 sm:p-5">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Ej: SUV, camioneta grande" required>
                    <div class="grid grid-cols-[1fr_auto] gap-3 items-center">
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="9999"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Orden">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="rounded border-gray-300" checked>
                            Activo
                        </label>
                    </div>
                    <button class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                        Crear tipo
                    </button>
                </form>

                <div class="min-h-0 flex-1 overflow-auto">
                    <table class="min-w-[460px] w-full table-fixed text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="w-[42%] px-3 py-3 text-center">Tipo</th>
                                <th class="w-[20%] px-3 py-3 text-center">Orden</th>
                                <th class="w-[18%] px-3 py-3 text-center">Estado</th>
                                <th class="w-[20%] px-3 py-3 text-center">Acc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicleTypes as $vehicleType)
                                @php($typeFormId = 'type-update-' . $vehicleType->id)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-3 text-center">
                                        <form id="{{ $typeFormId }}" method="POST"
                                            action="{{ route('admin.vehiculos.specifications.types.update', $vehicleType) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="active" value="0">
                                        </form>
                                        <input form="{{ $typeFormId }}" type="text" name="name" value="{{ $vehicleType->name }}"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-800">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <input form="{{ $typeFormId }}" type="number" name="sort_order" value="{{ $vehicleType->sort_order }}"
                                            min="0" max="9999"
                                            class="w-full rounded-lg border border-gray-200 px-2 py-2 text-center text-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <label class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-50 text-gray-700" title="Activo">
                                            <input form="{{ $typeFormId }}" type="checkbox" name="active" value="1"
                                                class="sr-only peer" @checked($vehicleType->active)>
                                            <x-heroicon-o-check-circle class="h-5 w-5 peer-checked:text-emerald-600" />
                                        </label>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button form="{{ $typeFormId }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100"
                                                title="Guardar" aria-label="Guardar">
                                                <x-heroicon-o-check class="h-4 w-4" />
                                            </button>
                                            <form method="POST" action="{{ route('admin.vehiculos.specifications.types.destroy', $vehicleType) }}"
                                                onsubmit="return confirm('¿Eliminar este tipo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50 disabled:opacity-30"
                                                    title="Eliminar" aria-label="Eliminar" @disabled($vehicleType->vehicles_count)>
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">No hay tipos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
