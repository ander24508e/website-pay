@forelse($items as $item)
    @php
        $presentations = collect($item['variantes'] ?? [])->values();
        $availablePresentations = $presentations->filter(fn ($presentation) => !($item['inventariable'] ?? false) || (int) ($presentation['stock'] ?? 0) > 0);
        $defaultPresentation = $availablePresentations->firstWhere('is_default', true)
            ?? $availablePresentations->first()
            ?? $presentations->first();
        $defaultPresentationStock = (int) ($defaultPresentation['stock'] ?? ($item['stock_disponible'] ?? 0));
    @endphp
    <div class="card item-catalogo">
        @if ($item['imagen'])
            <button
                type="button"
                class="card-image-wrap js-open-detail"
                data-id="{{ $item['id'] }}"
                data-tipo="{{ $item['tipo'] }}"
                data-nombre="{{ $item['nombre'] }}"
                data-categoria="{{ $item['categoria'] }}"
                data-precio="{{ $item['precio'] }}"
                data-descripcion="{{ $item['descripcion'] }}"
                data-tipo-descripcion="{{ $item['tipo_descripcion'] ?? '' }}"
                data-inventariable="{{ ($item['inventariable'] ?? false) ? '1' : '0' }}"
                data-comprable="{{ ($item['comprable'] ?? false) ? '1' : '0' }}"
                data-reservable="{{ ($item['reservable'] ?? false) ? '1' : '0' }}"
                data-requiere-tipo-vehiculo="{{ ($item['requiere_tipo_vehiculo'] ?? false) ? '1' : '0' }}"
                data-imagen="{{ Storage::url($item['imagen']) }}"
                aria-label="Ver detalle de {{ $item['nombre'] }}"
            >
                <img src="{{ Storage::url($item['imagen']) }}" alt="{{ $item['nombre'] }}" class="card-image">
            </button>
        @else
            <button
                type="button"
                class="card-image-wrap js-open-detail"
                data-id="{{ $item['id'] }}"
                data-tipo="{{ $item['tipo'] }}"
                data-nombre="{{ $item['nombre'] }}"
                data-categoria="{{ $item['categoria'] }}"
                data-precio="{{ $item['precio'] }}"
                data-descripcion="{{ $item['descripcion'] }}"
                data-tipo-descripcion="{{ $item['tipo_descripcion'] ?? '' }}"
                data-inventariable="{{ ($item['inventariable'] ?? false) ? '1' : '0' }}"
                data-comprable="{{ ($item['comprable'] ?? false) ? '1' : '0' }}"
                data-reservable="{{ ($item['reservable'] ?? false) ? '1' : '0' }}"
                data-requiere-tipo-vehiculo="{{ ($item['requiere_tipo_vehiculo'] ?? false) ? '1' : '0' }}"
                data-imagen=""
                aria-label="Ver detalle de {{ $item['nombre'] }}"
            >
                <div class="card-placeholder">
                    ITM
                </div>
            </button>
        @endif
        <div class="card-body">
            <div class="card-category">{{ $item['tipo_label'] ?? 'Catalogo' }} · {{ $item['categoria'] }}</div>
            <div class="card-top">
                <div class="card-name-row">
                    <div class="card-name">{{ $item['nombre'] }}</div>
                    @if(($item['comprable'] ?? false) && ($item['inventariable'] ?? false) && !($item['agotado'] ?? false) && $presentations->isNotEmpty())
                        <select class="catalog-variant-select js-card-variant-select" aria-label="Seleccionar presentacion">
                            @foreach ($presentations as $presentation)
                                @php($presentationStock = (int) ($presentation['stock'] ?? 0))
                                <option value="{{ $presentation['id'] }}"
                                    data-stock="{{ $presentationStock }}"
                                    data-price="{{ (float) ($presentation['price'] ?? 0) }}"
                                    {{ (int) ($defaultPresentation['id'] ?? 0) === (int) ($presentation['id'] ?? 0) ? 'selected' : '' }}
                                    {{ $presentationStock <= 0 ? 'disabled' : '' }}>
                                    {{ $presentation['name'] ?? 'Presentacion' }} - ${{ number_format((float) ($presentation['price'] ?? 0), 2) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="catalog-stock-counter" data-stock="{{ $defaultPresentationStock }}" data-quantity="1">
                            <button type="button" class="catalog-stock-btn js-stock-minus" aria-label="Restar cantidad" disabled>−</button>
                            <span class="catalog-stock-value js-stock-value">1</span>
                            <button type="button" class="catalog-stock-btn js-stock-plus" aria-label="Sumar cantidad" {{ $defaultPresentationStock <= 1 ? 'disabled' : '' }}>+</button>
                        </div>
                    @elseif(($item['comprable'] ?? false) && ($item['inventariable'] ?? false) && ($item['agotado'] ?? false))
                        <span class="catalog-stock-empty">Agotado</span>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                @if(($item['comprable'] ?? false) && !($item['agotado'] ?? false))
                    <button type="button"
                        class="btn-reservar-main {{ ($item['inventariable'] ?? false) ? 'js-add-counter-cart' : (($item['requiere_tipo_vehiculo'] ?? false) ? 'js-open-priced-service' : 'js-add-simple-cart') }}"
                        data-id="{{ $item['id'] }}"
                        data-tipo="{{ $item['tipo'] }}">
                        {{ ($item['inventariable'] ?? false) ? 'Comprar' : (($item['requiere_tipo_vehiculo'] ?? false) ? 'Buscar vehiculo' : 'Agregar servicio') }}
                    </button>
                @endif
                @if($item['reservable'] ?? false)
                    <button
                        type="button"
                        class="btn-reservar btn-reservar-main {{ ($item['requiere_tipo_vehiculo'] ?? false) ? 'js-open-priced-service' : 'js-open-reserve' }}"
                        data-id="{{ $item['id'] }}"
                        data-tipo="{{ $item['tipo'] }}"
                        data-nombre="{{ $item['nombre'] }}"
                        data-precio="{{ $item['precio'] }}"
                        title="Reservar" aria-label="Reservar">
                        Reservar
                    </button>
                @endif
            </div>
        </div>
    </div>
@empty
    <p class="catalogo-empty">No hay items disponibles.</p>
@endforelse
