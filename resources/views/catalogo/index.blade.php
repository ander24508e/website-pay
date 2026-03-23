<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --red:       #d82128;
            --red-dark:  #b41b21;
            --gold:      #f0b429;
            --dark:      #1e1e1e;
            --dark-2:    #141414;
            --dark-3:    #0a0a0a;
            --muted:     #666666;
            --light:     #f5f5f5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-3);
            color: white;
            overflow-x: hidden;
        }

        h1, h2, h3, .brand {
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 0.05em;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            height: 70px;
            background: rgba(10,10,10,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(216,33,40,0.2);
            transition: background 0.3s;
        }

        .navbar-brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: 0.08em;
            color: white;
            text-decoration: none;
        }

        .navbar-brand span { color: var(--red); }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .navbar-links a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .navbar-links a:hover { color: var(--gold); }

        .btn-login {
            background: var(--red);
            color: white !important;
            padding: 0.5rem 1.4rem;
            border-radius: 4px;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.12em !important;
            transition: background 0.2s !important;
        }

        .btn-login:hover { background: var(--red-dark) !important; color: white !important; }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: var(--dark-3);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 60% at 70% 50%, rgba(216,33,40,0.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(240,180,41,0.06) 0%, transparent 60%);
        }

        /* Diagonal stripe pattern */
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 40px,
                rgba(255,255,255,0.01) 40px,
                rgba(255,255,255,0.01) 41px
            );
        }

        /* Red accent bar */
        .hero-bg::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, transparent, var(--red), transparent);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
            max-width: 900px;
        }

        .hero-eyebrow {
            display: inline-block;
            background: rgba(216,33,40,0.15);
            border: 1px solid rgba(216,33,40,0.4);
            color: var(--red);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.4rem 1.2rem;
            border-radius: 2px;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s ease both;
        }

        .hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(3.5rem, 9vw, 7rem);
            line-height: 1;
            letter-spacing: 0.04em;
            color: white;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .hero-title .accent { color: var(--red); }
        .hero-title .gold   { color: var(--gold); }

        .hero-sub {
            font-size: 1rem;
            color: rgba(255,255,255,0.55);
            margin: 1.5rem auto 2.5rem;
            max-width: 500px;
            line-height: 1.7;
            font-weight: 500;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-primary {
            background: var(--red);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid var(--red);
        }

        .btn-primary:hover {
            background: var(--red-dark);
            border-color: var(--red-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(216,33,40,0.4);
        }

        .btn-outline {
            background: transparent;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        /* Stats bar */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 4rem;
            margin-top: 5rem;
            padding-top: 3rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            animation: fadeUp 0.6s 0.4s ease both;
        }

        .stat-item { text-align: center; }

        .stat-number {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.5rem;
            color: var(--gold);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        /* ── SECTION COMMON ── */
        .section {
            padding: 6rem 2rem;
        }

        .section-dark { background: var(--dark-2); }
        .section-darker { background: var(--dark-3); }

        .section-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .section-tag {
            display: inline-block;
            background: rgba(216,33,40,0.12);
            border: 1px solid rgba(216,33,40,0.3);
            color: var(--red);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 2px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            color: white;
            line-height: 1.1;
        }

        .section-title span { color: var(--red); }

        .section-sub {
            font-size: 0.9rem;
            color: var(--muted);
            margin-top: 0.75rem;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        /* ── CARRUSEL ── */
        .carousel-wrapper {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            gap: 1.5rem;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* ── CARDS ── */
        .card {
            flex: 0 0 calc(33.333% - 1rem);
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        @media (max-width: 768px) {
            .card { flex: 0 0 calc(100% - 1rem); }
        }

        .card:hover {
            border-color: rgba(216,33,40,0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.4);
        }

        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, rgba(216,33,40,0.1), rgba(240,180,41,0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-category {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }

        .card-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            letter-spacing: 0.04em;
            color: white;
            margin-bottom: 0.5rem;
        }

        .card-desc {
            font-size: 0.8rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-price {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            color: var(--red);
            letter-spacing: 0.04em;
        }

        .card-price span {
            font-size: 1rem;
            color: var(--muted);
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .btn-card {
            background: var(--red);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-card:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
        }

        /* Carousel controls */
        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
        }

        .carousel-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.15);
            background: transparent;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-btn:hover {
            border-color: var(--red);
            background: var(--red);
        }

        /* ── CONTACTO ── */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .contact-grid { grid-template-columns: 1fr; }
        }

        .contact-info { }

        .contact-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: flex-start;
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: rgba(216,33,40,0.12);
            border: 1px solid rgba(216,33,40,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .contact-text h4 {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.3rem;
        }

        .contact-text p {
            font-size: 0.95rem;
            color: white;
            font-weight: 500;
        }

        .map-container {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
            height: 350px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* ── FOOTER ── */
        .footer {
            background: var(--dark-3);
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 3rem 2rem 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
        }

        .footer-brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            color: white;
            margin-bottom: 0.75rem;
        }

        .footer-brand span { color: var(--red); }

        .footer-desc {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 280px;
        }

        .footer-title {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .footer-links a {
            font-size: 0.82rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: white; }

        .footer-bottom {
            max-width: 1100px;
            margin: 2.5rem auto 0;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-copy {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .footer-copy span { color: var(--red); }

        /* ── DIVIDER ── */
        .divider {
            width: 60px;
            height: 3px;
            background: var(--red);
            margin: 0.75rem auto 0;
            border-radius: 2px;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .fade-up {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

{{-- ══ NAVBAR ══ --}}
<nav class="navbar">
    <a href="#inicio" class="navbar-brand">
        ENDARA <span>CARWASH</span>
    </a>
    <ul class="navbar-links">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#servicios">Servicios</a></li>
        <li><a href="#productos">Productos</a></li>
        <li><a href="#contacto">Contacto</a></li>
        @auth
            <li><a href="{{ route('admin.dashboard') }}" class="btn-login">Panel Admin</a></li>
        @else
            <li><a href="{{ route('login') }}" class="btn-login">Acceder</a></li>
        @endauth
    </ul>
</nav>

{{-- ══ HERO ══ --}}
<section class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">🚗 Servicio profesional de lavado y lubricación</div>
        <h1 class="hero-title">
            LAVADORA Y<br>
            <span class="accent">LUBRICADORA</span><br>
            <span class="gold">ENDARA</span>
        </h1>
        <p class="hero-sub">
            Tu vehículo merece el mejor cuidado. Lavado completo, express, premium y servicios de lubricación profesional en un solo lugar.
        </p>
        <div class="hero-actions">
            <a href="#servicios" class="btn-primary">Ver Servicios</a>
            <a href="#contacto" class="btn-outline">Contáctanos</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-number">7+</div>
                <div class="stat-label">Servicios disponibles</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Satisfacción garantizada</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Atención al cliente</div>
            </div>
        </div>
    </div>
</section>

{{-- ══ SERVICIOS ══ --}}
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
                    <img src="{{ Storage::url($service->image) }}"
                         alt="{{ $service->name }}"
                         class="card-image">
                @else
                    <div class="card-placeholder">
                        @if(str_contains(strtolower($service->name), 'motor'))  🔧
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
                        <div class="card-price">
                            ${{ number_format($service->price, 2) }}
                            <span>/ servicio</span>
                        </div>
                        <a href="{{ route('carrito.agregar') }}" class="btn-card"
                           onclick="event.preventDefault(); addToCart({{ $service->id }}, 'service')">
                            Agregar
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p style="color: var(--muted); text-align:center; width:100%;">No hay servicios disponibles.</p>
            @endforelse
        </div>
    </div>

    <div class="carousel-controls">
        <button class="carousel-btn" onclick="slide('servicios-track', -1)">&#8592;</button>
        <button class="carousel-btn" onclick="slide('servicios-track', 1)">&#8594;</button>
    </div>
</section>

{{-- ══ PRODUCTOS ══ --}}
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
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         class="card-image">
                @else
                    <div class="card-placeholder">🛢️</div>
                @endif
                <div class="card-body">
                    <div class="card-category">{{ $product->category->name ?? 'Producto' }}</div>
                    <div class="card-name">{{ $product->name }}</div>
                    <p class="card-desc">{{ $product->description }}</p>
                    <div class="card-footer">
                        <div class="card-price">
                            ${{ number_format($product->price, 2) }}
                            <span>/ unidad</span>
                        </div>
                        <a href="#" class="btn-card"
                           onclick="event.preventDefault(); addToCart({{ $product->id }}, 'product')">
                            Agregar
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p style="color: var(--muted); text-align:center; width:100%;">No hay productos disponibles.</p>
            @endforelse
        </div>
    </div>

    <div class="carousel-controls">
        <button class="carousel-btn" onclick="slide('productos-track', -1)">&#8592;</button>
        <button class="carousel-btn" onclick="slide('productos-track', 1)">&#8594;</button>
    </div>
</section>

{{-- ══ CONTACTO ══ --}}
<section class="section section-dark" id="contacto">
    <div class="section-header fade-up">
        <div class="section-tag">Encuéntranos</div>
        <h2 class="section-title">CONTACTO Y <span>UBICACIÓN</span></h2>
        <div class="divider"></div>
    </div>

    <div class="contact-grid fade-up">
        <div class="contact-info">
            <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div class="contact-text">
                    <h4>Dirección</h4>
                    <p>{{ $empresa->direccion ?? 'Cayambe, Pichincha, Ecuador' }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-text">
                    <h4>Teléfono</h4>
                    <p>{{ $empresa->telefono ?? '+593 99 999 9999' }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">⏰</div>
                <div class="contact-text">
                    <h4>Horario de atención</h4>
                    <p>Lunes a Sábado: 8:00 — 18:00</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">🚗</div>
                <div class="contact-text">
                    <h4>Servicios</h4>
                    <p>Lavado · Lubricación · Mantenimiento</p>
                </div>
            </div>
        </div>

        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.7!2d-78.14!3d0.04!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMMKwMDInMjQuMCJOIDc4wrAwOCczNi4wIlc!5e0!3m2!1ses!2sec!4v1"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

{{-- ══ FOOTER ══ --}}
<footer class="footer">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">ENDARA <span>CARWASH</span></div>
            <p class="footer-desc">
                Lavadora y Lubricadora Endara — Tu vehículo en las mejores manos. Servicio profesional, productos de calidad y atención personalizada.
            </p>
        </div>
        <div>
            <div class="footer-title">Servicios</div>
            <ul class="footer-links">
                <li><a href="#servicios">Lavada Completa</a></li>
                <li><a href="#servicios">Lavada Express</a></li>
                <li><a href="#servicios">Lavada Premium</a></li>
                <li><a href="#servicios">Lubricación de Motor</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-title">Accesos</div>
            <ul class="footer-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#productos">Productos</a></li>
                <li><a href="#contacto">Contacto</a></li>
                @guest
                <li><a href="{{ route('login') }}">Administrador</a></li>
                @endguest
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p class="footer-copy">
            © {{ date('Y') }} <span>Lavadora y Lubricadora Endara</span>. Todos los derechos reservados.
        </p>
        <p class="footer-copy">Hecho con ❤️ en Cayambe, Ecuador</p>
    </div>
</footer>

{{-- Toast notification --}}
<div id="toast" style="
    position: fixed; bottom: 2rem; right: 2rem;
    background: #1e1e1e; border: 1px solid rgba(216,33,40,0.4);
    color: white; padding: 1rem 1.5rem; border-radius: 8px;
    font-size: 0.85rem; font-weight: 600;
    display: none; z-index: 9999;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    animation: fadeUp 0.3s ease;
">
    ✅ Agregado al carrito
</div>

<script>
// ── Carrusel ──
function slide(trackId, direction) {
    const track = document.getElementById(trackId);
    const card = track.querySelector('.card');
    if (!card) return;
    const cardWidth = card.offsetWidth + 24; // gap
    const current = parseInt(track.dataset.offset || 0);
    const cards = track.querySelectorAll('.card');
    const max = Math.max(0, cards.length - 3);
    const newOffset = Math.max(0, Math.min(current + direction, max));
    track.dataset.offset = newOffset;
    track.style.transform = `translateX(-${newOffset * cardWidth}px)`;
}

// ── Agregar al carrito ──
function addToCart(id, type) {
    fetch('{{ route("carrito.agregar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id, type, quantity: 1 })
    }).then(res => {
        if (res.ok || res.status === 302) {
            showToast('✅ Agregado al carrito');
        }
    }).catch(() => {
        showToast('✅ Agregado al carrito');
    });
}

function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 3000);
}

// ── Scroll animations ──
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>

</body>
</html>