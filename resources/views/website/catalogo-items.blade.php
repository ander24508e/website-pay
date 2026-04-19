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
            <button class="btn-card" onclick="addToCart({{ $item['id'] }}, '{{ $item['tipo'] }}')">Agregar</button>
        </div>
    </div>
</div>
@empty
<p class="catalogo-empty">No hay productos o servicios disponibles.</p>
@endforelse
