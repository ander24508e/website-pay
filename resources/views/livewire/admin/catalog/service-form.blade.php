<div class="mx-auto w-full max-w-[1500px] px-3 pb-4 sm:px-5 xl:h-[calc(100vh-2rem)] xl:overflow-hidden">
    @php
        $isEditing = (bool) $catalogItemId;
        $inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300';
        $labelClass = 'mb-1 block text-xs font-semibold text-gray-700';
    @endphp

    @include('partials.admin-notifications')

    <style>
        @keyframes servicePricePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(17, 24, 39, 0); transform: translateY(0); }
            35% { box-shadow: 0 0 0 6px rgba(17, 24, 39, 0.12); transform: translateY(-1px); }
            70% { box-shadow: 0 0 0 12px rgba(17, 24, 39, 0); transform: translateY(0); }
        }

        .service-price-highlight {
            animation: servicePricePulse 1.25s ease-in-out 3;
        }
    </style>

    <div class="mb-3 flex items-center gap-3">
        <a href="{{ $returnUrl }}"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-800"
            aria-label="Volver">
            <span aria-hidden="true">&larr;</span>
        </a>

        <div class="min-w-0">
            <h2 class="text-xl font-bold leading-tight text-gray-900 sm:text-2xl">
                {{ $isEditing ? 'Editar Servicio' : 'Nuevo Servicio' }}
            </h2>
            <p class="text-sm text-gray-400">
                {{ $isEditing ? 'Actualiza la informacion general del servicio.' : 'Crea la informacion general del servicio.' }}
            </p>
        </div>
    </div>

    @if ($types->isEmpty())
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
            Primero crea un negocio configurado como servicios.
            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('admin.catalog-types.create', ['business_model' => 'services']) }}"
                    class="inline-flex justify-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Crear negocio de servicios
                </a>
                <a href="{{ $returnUrl }}"
                    class="inline-flex justify-center rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                    Volver
                </a>
            </div>
        </div>
    @else
        <form wire:submit.prevent="save"
            class="rounded-lg bg-white shadow-sm xl:flex xl:h-[calc(100%-4.25rem)] xl:flex-col xl:overflow-hidden">
            <div class="grid gap-4 p-4 xl:grid-cols-[minmax(0,1fr)_300px] xl:overflow-y-auto">
                <div class="min-w-0 space-y-4">
                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Imagen</p>
                        <div class="grid gap-3 sm:grid-cols-[150px_minmax(0,1fr)] sm:items-center">
                            <div class="mx-auto flex h-28 w-28 items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-200 bg-white text-center text-xs text-gray-400 sm:h-32 sm:w-32">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover" alt="Vista previa del servicio">
                                @elseif ($currentImage)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($currentImage) }}" class="h-full w-full object-cover" alt="Imagen actual del servicio">
                                @else
                                    Sin imagen
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="mb-2 truncate text-xs text-gray-400">
                                    <span wire:loading.remove wire:target="image">
                                        {{ $image ? $image->getClientOriginalName() : (basename((string) $currentImage) ?: 'Sin imagen seleccionada') }}
                                    </span>
                                    <span wire:loading wire:target="image">Cargando imagen...</span>
                                </p>

                                <label class="inline-flex w-full cursor-pointer justify-center rounded-lg bg-gray-900 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 sm:max-w-xs">
                                    {{ $currentImage || $image ? 'Cambiar imagen' : 'Subir imagen' }}
                                    <input type="file" wire:model="image" accept="image/*" class="hidden">
                                </label>

                                <p class="mt-2 text-xs text-gray-400">JPG, PNG o WEBP - Max. 6 MB</p>
                                @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Informacion basica</p>
                            <p class="text-xs text-gray-500">Nombre, categoria y descripcion del servicio.</p>
                        </div>

                        <div>
                            <label for="serviceName" class="{{ $labelClass }}">Nombre *</label>
                            <input id="serviceName" type="text" wire:model="name"
                                class="{{ $inputClass }} @error('name') border-red-400 bg-red-50 @enderror"
                                placeholder="Ej.: Lavada completa">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="flex min-w-0 flex-col">
                                <div class="mb-1 flex min-h-8 items-center">
                                    <label class="block text-xs font-semibold text-gray-700">Negocio</label>
                                </div>
                                <input type="text" value="{{ $selectedType?->name }}"
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 outline-none transition focus:ring-2 focus:ring-gray-300"
                                    readonly>
                                @error('selectedTypeId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex min-w-0 flex-col">
                                <div class="mb-1 flex min-h-8 items-center justify-between gap-2">
                                    <label for="selectedCategoryId" class="block text-xs font-semibold text-gray-700">Categoria</label>
                                    <button type="button" wire:click="openCategoryModal"
                                        class="inline-flex h-8 shrink-0 items-center justify-center gap-1 whitespace-nowrap rounded-lg bg-gray-100 px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-200">
                                        <x-heroicon-o-plus class="h-4 w-4" />
                                        Nueva categoria
                                    </button>
                                </div>

                                @if ($newCategoryName)
                                    <input type="text" value="{{ $newCategoryName }}"
                                        class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 outline-none transition focus:ring-2 focus:ring-gray-300"
                                        readonly>
                                @else
                                    <select id="selectedCategoryId" wire:model="selectedCategoryId" wire:change="clearInlineCategory"
                                        class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 outline-none transition focus:ring-2 focus:ring-gray-300 @error('selectedCategoryId') border-red-400 bg-red-50 @enderror">
                                        <option value="">Sin categoria</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('selectedCategoryId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                @error('newCategoryName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="serviceDescription" class="{{ $labelClass }}">Descripcion</label>
                            <textarea id="serviceDescription" wire:model="description" rows="3"
                                class="{{ $inputClass }} resize-none @error('description') border-red-400 bg-red-50 @enderror"
                                placeholder="Describe este servicio y que incluye"></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </section>
                </div>

                <aside class="space-y-4">
                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Precios por vehiculo</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Configura cuanto cuesta este servicio segun el tipo interno del vehiculo.
                        </p>

                        @if ($catalogItemId)
                            <button type="button" wire:click="openPricePanel" wire:key="service-price-action-{{ $highlightKey }}"
                                class="{{ $highlightPrices ? 'service-price-highlight' : '' }} mt-4 inline-flex h-10 w-full items-center justify-center rounded-lg bg-gray-900 px-4 text-sm font-semibold text-white transition hover:bg-gray-700">
                                Precios por vehiculo
                            </button>
                            <p class="mt-2 text-center text-xs text-gray-500">
                                {{ $priceCount }} {{ $priceCount === 1 ? 'precio agregado' : 'precios agregados' }}
                            </p>
                        @else
                            <p class="mt-4 rounded-lg border border-gray-100 bg-white px-3 py-3 text-xs text-gray-500">
                                Despues de guardar el servicio, este boton se activara para continuar.
                            </p>
                        @endif
                    </section>

                    <section class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Estado</p>
                        <div class="space-y-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                <input type="checkbox" wire:model="active" class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Activo</span>
                                    <span class="block text-xs text-gray-400">Visible en catalogo</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                <input type="checkbox" wire:model="reservable" class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Reservable</span>
                                    <span class="block text-xs text-gray-400">Disponible para agendar</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-100 bg-white p-3">
                                <input type="checkbox" wire:model="featured" class="mt-1 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-700">Destacado</span>
                                    <span class="block text-xs text-gray-400">Mayor prioridad en la web</span>
                                </span>
                            </label>
                        </div>
                    </section>
                </aside>
            </div>

            <div class="flex flex-col gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                <a href="{{ $returnUrl }}"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700 disabled:cursor-wait disabled:opacity-70"
                    wire:loading.attr="disabled" wire:target="save,image">
                    <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Actualizar servicio' : 'Guardar servicio' }}</span>
                    <span wire:loading wire:target="save">Guardando...</span>
                </button>
            </div>
        </form>
    @endif

    @if ($showCategoryModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto px-4 py-6" role="dialog" aria-modal="true">
            <button type="button" class="fixed inset-0 cursor-default bg-gray-900/50 backdrop-blur-[1px]" wire:click="closeCategoryModal" aria-label="Cerrar modal"></button>
            <div class="relative z-10 w-full max-w-md overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Nueva categoria</h3>
                        <p class="mt-1 text-sm text-gray-500">Se creara dentro del negocio actual.</p>
                    </div>
                    <button type="button" wire:click="closeCategoryModal" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label class="{{ $labelClass }}">Nombre *</label>
                        <input type="text" wire:model="newCategoryName"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm outline-none transition focus:ring-2 focus:ring-gray-300 @error('newCategoryName') border-red-400 bg-red-50 @enderror"
                            placeholder="Ej.: Exterior">
                        @error('newCategoryName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Descripcion</label>
                        <textarea wire:model="newCategoryDescription" rows="3"
                            class="w-full resize-none rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-gray-300"
                            placeholder="Opcional"></textarea>
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="closeCategoryModal"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="button" wire:click="applyCategory"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700">
                        Usar categoria
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showPricePanel)
        <div class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto px-3 py-5" role="dialog" aria-modal="true">
            <button type="button" class="fixed inset-0 cursor-default bg-gray-900/50 backdrop-blur-[1px]" wire:click="closePricePanel" aria-label="Cerrar precios"></button>
            <div class="relative z-10 flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900">Precios por vehiculo</h3>
                        <p class="mt-1 text-sm text-gray-500">Agrega tipos internos, precios, duracion e insumos usados.</p>
                    </div>
                    <button type="button" wire:click="closePricePanel" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto p-4">
                    <div class="mb-4 grid gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                        <input type="text" wire:model="newVehicleTypeName"
                            class="{{ $inputClass }} @error('newVehicleTypeName') border-red-400 bg-red-50 @enderror"
                            placeholder="Nuevo tipo de vehiculo, ej.: Auto pequeno, SUV, Camioneta">
                        <button type="button" wire:click="addVehicleType"
                            class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-4 text-sm font-semibold text-white transition hover:bg-gray-700">
                            Crear tipo
                        </button>
                        @error('newVehicleTypeName') <p class="text-xs text-red-500 sm:col-span-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-4">
                        @foreach ($priceRows as $index => $row)
                            <section class="rounded-xl border border-gray-100 bg-gray-50 p-3" wire:key="price-row-{{ $index }}">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Precio {{ $index + 1 }}</p>
                                    <button type="button" wire:click="removePriceRow({{ $index }})" class="text-xs font-semibold text-red-500 hover:text-red-700">
                                        Eliminar
                                    </button>
                                </div>

                                <div class="grid gap-3 md:grid-cols-4">
                                    <div class="md:col-span-1">
                                        <label class="{{ $labelClass }}">Tipo de vehiculo *</label>
                                        <select wire:model="priceRows.{{ $index }}.vehicle_type_id"
                                            class="{{ $inputClass }} @error('priceRows.' . $index . '.vehicle_type_id') border-red-400 bg-red-50 @enderror">
                                            <option value="">Selecciona tipo</option>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="{{ $labelClass }}">Precio *</label>
                                        <input type="number" wire:model="priceRows.{{ $index }}.price" step="0.01" min="0"
                                            class="{{ $inputClass }} @error('priceRows.' . $index . '.price') border-red-400 bg-red-50 @enderror"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="{{ $labelClass }}">Duracion</label>
                                        <input type="number" wire:model="priceRows.{{ $index }}.duration_minutes" min="1" step="1"
                                            class="{{ $inputClass }}" placeholder="Minutos">
                                    </div>
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-100 bg-white px-3 py-2">
                                        <input type="checkbox" wire:model="priceRows.{{ $index }}.active" class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                        <span class="text-sm font-semibold text-gray-700">Activo</span>
                                    </label>
                                </div>

                                <div class="mt-3">
                                    <label class="{{ $labelClass }}">Descripcion</label>
                                    <textarea wire:model="priceRows.{{ $index }}.description" rows="2"
                                        class="{{ $inputClass }} resize-none" placeholder="Notas para este tipo de vehiculo"></textarea>
                                </div>

                                <div class="mt-4">
                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Insumos usados</p>
                                    @if ($supplyVariants->isEmpty())
                                        <p class="rounded-lg border border-gray-100 bg-white p-3 text-xs text-gray-500">
                                            No hay productos inventariables para usar como insumos.
                                        </p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach (($row['supplies'] ?? []) as $supplyIndex => $supply)
                                                <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_120px_120px]" wire:key="price-row-{{ $index }}-supply-{{ $supplyIndex }}">
                                                    <select wire:model="priceRows.{{ $index }}.supplies.{{ $supplyIndex }}.catalog_item_variant_id"
                                                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm">
                                                        <option value="">Selecciona insumo</option>
                                                        @foreach ($supplyVariants as $variant)
                                                            <option value="{{ $variant->id }}">
                                                                {{ $variant->item?->name }} / {{ $variant->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" wire:model="priceRows.{{ $index }}.supplies.{{ $supplyIndex }}.quantity"
                                                        step="0.001" min="0.001"
                                                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm"
                                                        placeholder="Cantidad">
                                                    <input type="text" wire:model="priceRows.{{ $index }}.supplies.{{ $supplyIndex }}.unit"
                                                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm"
                                                        placeholder="Unidad">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addPriceRow"
                        class="mt-4 inline-flex h-10 w-full items-center justify-center rounded-lg bg-gray-100 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                        Agregar otro precio
                    </button>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 p-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="closePricePanel"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-100 px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="button" wire:click="savePriceRows"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700 disabled:cursor-wait disabled:opacity-70"
                        wire:loading.attr="disabled" wire:target="savePriceRows">
                        <span wire:loading.remove wire:target="savePriceRows">Guardar precios</span>
                        <span wire:loading wire:target="savePriceRows">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
