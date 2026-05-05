@forelse($items as $item)
    <div class="card item-catalogo">
        @if ($item['imagen'])
            <div class="card-image-wrap">
                <img src="{{ Storage::url($item['imagen']) }}" alt="{{ $item['nombre'] }}" class="card-image">
            </div>
        @else
            <div class="card-image-wrap">
                <div class="card-placeholder">
                    {{ $item['tipo'] === 'product' ? 'PRD' : 'SRV' }}
                </div>
            </div>
        @endif
        <div class="card-body">
            <div class="card-category">{{ $item['categoria'] }}</div>
            <div class="card-top">
                <div class="card-name-row">
                    <div class="card-name">{{ $item['nombre'] }}</div>
                    <button class="card-name-cart-btn" onclick="addToCart({{ $item['id'] }}, '{{ $item['tipo'] }}')"
                        title="Agregar al carrito" aria-label="Agregar al carrito">
                        <x-heroicon-s-shopping-cart class="w-5 h-5" />
                    </button>
                </div>
            </div>
            <p class="card-desc">{{ Str::limit($item['descripcion'], 80) }}</p>
            <div class="card-footer">
                <button type="button" onclick="reserveItem({{ $item['id'] }}, '{{ $item['tipo'] }}')"
                    class="btn-reservar btn-reservar-main" title="Reservar" aria-label="Reservar">
                    Reservar
                </button>
            </div>
        </div>
    </div>
@empty
    <p class="catalogo-empty">No hay productos o servicios disponibles.</p>
@endforelse
