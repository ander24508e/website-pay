<section class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">🚗 {{ $empresa->eslogan_texto }}</div>
        <h1 class="hero-title">
            {{ strtoupper($empresa->nombre ?? 'Lavadora y Lubricadora') }}
        </h1>
        <p class="hero-sub">{{ $empresa->descripcion_corta_texto }}</p>
        <div class="hero-actions">
            <a href="#catalogo" class="btn-primary">Ver Catalogo</a>
            <a href="#contacto" class="btn-outline">Contactanos</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $empresa->correo_contacto }}</div>
                <div class="stat-label">Correo</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $empresa->ciudad_texto }}</div>
                <div class="stat-label">Ciudad</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $empresa->telefono_contacto }}</div>
                <div class="stat-label">Llamanos</div>
            </div>
        </div>
    </div>
</section>
