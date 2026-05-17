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
                    @if($item['comprable'] ?? false)
                        <button class="card-name-cart-btn" onclick="addToCart({{ $item['id'] }}, '{{ $item['tipo'] }}')"
                            title="Agregar al carrito" aria-label="Agregar al carrito">
                            <x-heroicon-s-shopping-cart class="w-5 h-5" />
                        </button>
                    @endif
                </div>
            </div>
            <p class="card-desc">{{ Str::limit($item['descripcion'], 80) }}</p>
            <div class="card-footer">
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
