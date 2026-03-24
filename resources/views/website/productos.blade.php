<section class="section section-darker" id="productos">
    <div class="section-header fade-up">
        <div class="section-tag">Lo que vendemos</div>
        <h2 class="section-title">NUESTROS <span>PRODUCTOS</span></h2>
        <div class="divider"></div>
        <p class="section-sub">Aceites y lubricantes de las mejores marcas para mantener tu motor en perfectas condiciones.</p>
    </div>
    <div class="carousel-wrapper">
        <div class="carousel-track" id="productos-track">
            @forelse($products as $product)
            <div class="card">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="card-image">
                @else
                    <div class="card-placeholder">🛢️</div>
                @endif
                <div class="card-body">
                    <div class="card-category">{{ $product->category->name ?? 'Producto' }}</div>
                    <div class="card-name">{{ $product->name }}</div>
                    <p class="card-desc">{{ $product->description }}</p>
                    <div class="card-footer">
                        <div class="card-price">${{ number_format($product->price, 2) }}<span>/ unidad</span></div>
                        <button class="btn-card" onclick="addToCart({{ $product->id }}, 'product')">Agregar</button>
                    </div>
                </div>
            </div>
            @empty
                <p style="color:var(--muted);text-align:center;width:100%;">No hay productos disponibles.</p>
            @endforelse
        </div>
    </div>
    <div class="carousel-controls">
        <button class="carousel-btn" onclick="slide('productos-track', -1)">&#8592;</button>
        <button class="carousel-btn" onclick="slide('productos-track',  1)">&#8594;</button>
    </div>
</section>