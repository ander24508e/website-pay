@forelse($items as $item)
<div class="card item-catalogo">
    @if($item['imagen'])
        <img src="{{ Storage::url($item['imagen']) }}" alt="{{ $item['nombre'] }}" class="card-image">
    @else
        <div class="card-placeholder">
            {{ $item['tipo'] === 'product' ? 'PRD' : 'SRV' }}
        </div>
    @endif
    <div class="card-body">
        <div class="card-category">{{ $item['categoria'] }}</div>
        <div class="card-name">{{ $item['nombre'] }}</div>
        <p class="card-desc">{{ Str::limit($item['descripcion'], 80) }}</p>
        <div class="card-footer">
            <div class="card-price">${{ number_format($item['precio'], 2) }}<span>/ {{ $item['tipo'] === 'product' ? 'unidad' : 'servicio' }}</span></div>
            <div class="card-actions">
                @if(($item['tipo'] ?? '') === 'service' && ($item['reservable'] ?? false))
                    <button
                        type="button"
                        onclick="reserveService({{ $item['id'] }})"
                        class="btn-reservar"
                        title="Reservar servicio"
                        aria-label="Reservar servicio de lavada"
                    >
                        Reservar
                    </button>
                @endif
                <button
                    class="btn-card-icon"
                    onclick="addToCart({{ $item['id'] }}, '{{ $item['tipo'] }}')"
                    title="Agregar al carrito"
                    aria-label="Agregar al carrito"
                >
                    <x-heroicon-s-shopping-cart class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
</div>
@empty
<p class="catalogo-empty">No hay productos o servicios disponibles.</p>
@endforelse
