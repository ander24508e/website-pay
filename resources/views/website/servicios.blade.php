<section class="section section-dark" id="servicios">
    <div class="section-header fade-up">
        <div class="section-tag">Lo que hacemos</div>
        <h2 class="section-title">NUESTROS <span>SERVICIOS</span></h2>
        <div class="divider"></div>
        <p class="section-sub">Desde un lavado rápido hasta lubricación completa del motor, ofrecemos soluciones para mantener tu vehículo en óptimas condiciones.</p>
    </div>

    <div class="carousel-wrapper">
        <div class="carousel-track" id="servicios-track">
            @forelse($services as $service)
            <div class="card">
                @if($service->image)
                    <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="card-image">
                @else
                    <div class="card-placeholder">
                        @if(str_contains(strtolower($service->name), 'motor')) 🔧
                        @elseif(str_contains(strtolower($service->name), 'lubri')) 🛢️
                        @else 🚗
                        @endif
                    </div>
                @endif
                <div class="card-body">
                    <div class="card-category">{{ $service->category->name ?? 'Servicio' }}</div>
                    <div class="card-name">{{ $service->name }}</div>
                    <p class="card-desc">{{ $service->description }}</p>
                    <div class="card-footer">
                        <div class="card-price">${{ number_format($service->price, 2) }}<span>/ servicio</span></div>
                        <button class="btn-card" onclick="addToCart({{ $service->id }}, 'service')">Agregar</button>
                    </div>
                </div>
            </div>
            @empty
                <p style="color:var(--muted);text-align:center;width:100%;">No hay servicios disponibles.</p>
            @endforelse
        </div>
    </div>

    <div class="carousel-controls">
        <button class="carousel-btn" onclick="slide('servicios-track', -1)">&#8592;</button>
        <button class="carousel-btn" onclick="slide('servicios-track',  1)">&#8594;</button>
    </div>
</section>