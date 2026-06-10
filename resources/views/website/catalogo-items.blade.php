@forelse($items as $item)
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
                    @if(($item['comprable'] ?? false) && ($item['inventariable'] ?? false) && !($item['agotado'] ?? false))
                        <div class="catalog-stock-counter" data-stock="{{ (int) ($item['stock_disponible'] ?? 9999) }}" data-quantity="1">
                            <button type="button" class="catalog-stock-btn js-stock-minus" aria-label="Restar cantidad" disabled>−</button>
                            <span class="catalog-stock-value js-stock-value">1</span>
                            <button type="button" class="catalog-stock-btn js-stock-plus" aria-label="Sumar cantidad" {{ (int) ($item['stock_disponible'] ?? 9999) <= 1 ? 'disabled' : '' }}>+</button>
                        </div>
                    @elseif(($item['comprable'] ?? false) && ($item['inventariable'] ?? false) && ($item['agotado'] ?? false))
                        <span class="catalog-stock-empty">Agotado</span>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                @if(($item['comprable'] ?? false) && !($item['agotado'] ?? false))
                    <button type="button"
                        class="btn-reservar-main {{ ($item['inventariable'] ?? false) ? 'js-add-counter-cart' : 'js-add-simple-cart' }}"
                        data-id="{{ $item['id'] }}"
                        data-tipo="{{ $item['tipo'] }}">
                        {{ ($item['inventariable'] ?? false) ? 'Comprar' : 'Agregar servicio' }}
                    </button>
                @endif
                @if($item['reservable'] ?? false)
                    <button
                        type="button"
                        class="btn-reservar btn-reservar-main js-open-reserve"
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
