@php
    $waPhone = preg_replace('/\D+/', '', (string) $empresa->telefono_contacto);
    $waMessage = rawurlencode('¡Hola me gustaria mas informacion!');
    $waUrl = $waPhone ? "https://wa.me/{$waPhone}?text={$waMessage}" : '#';
@endphp

@php
    $landingBanners = $empresa->landingBanners->where('activo', true)->sortBy('orden')->values();
@endphp

@if($landingBanners->isNotEmpty())
    <section class="hero hero-carousel" id="inicio">
        <div class="hero-carousel-track" id="hero-carousel-track">
            @foreach($landingBanners as $index => $banner)
                <article class="hero-slide {{ $index === 0 ? 'is-active' : '' }}"
                         data-slide="{{ $index }}">
                    <img class="hero-slide-media"
                         src="{{ $banner->imagen_url }}"
                         alt="{{ $banner->titulo ?: 'Banner promocional' }}">
                    <div class="hero-slide-overlay"></div>
                    <div class="hero-slide-content">
                        @if($banner->titulo)
                            <div class="hero-eyebrow">Promocion destacada</div>
                            <h1 class="hero-title hero-title-banner">{{ $banner->titulo }}</h1>
                        @endif

                        @if($banner->texto)
                            <p class="hero-sub hero-sub-banner">{{ $banner->texto }}</p>
                        @endif

                        @if($banner->boton_texto && $banner->boton_link)
                            <div class="hero-actions">
                                <a href="{{ $banner->boton_link }}" class="btn-primary">{{ $banner->boton_texto }}</a>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($landingBanners->count() > 1)
            <button type="button" class="hero-control hero-control-prev" id="hero-prev" aria-label="Banner anterior">â€¹</button>
            <button type="button" class="hero-control hero-control-next" id="hero-next" aria-label="Banner siguiente">â€º</button>

            <div class="hero-dots" id="hero-dots">
                @foreach($landingBanners as $index => $banner)
                    <button type="button"
                            class="hero-dot {{ $index === 0 ? 'is-active' : '' }}"
                            data-dot="{{ $index }}"
                            aria-label="Ir al banner {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    @push('scripts')
    <script>
    (() => {
        const slides = Array.from(document.querySelectorAll('.hero-slide'));
        const dots = Array.from(document.querySelectorAll('.hero-dot'));
        const prev = document.getElementById('hero-prev');
        const next = document.getElementById('hero-next');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let autoplay = null;

        const render = (index) => {
            currentIndex = (index + slides.length) % slides.length;

            slides.forEach((slide, slideIndex) => {
                slide.classList.toggle('is-active', slideIndex === currentIndex);
            });

            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('is-active', dotIndex === currentIndex);
            });
        };

        const startAutoplay = () => {
            clearInterval(autoplay);
            autoplay = setInterval(() => render(currentIndex + 1), 6000);
        };

        prev?.addEventListener('click', () => {
            render(currentIndex - 1);
            startAutoplay();
        });

        next?.addEventListener('click', () => {
            render(currentIndex + 1);
            startAutoplay();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                render(index);
                startAutoplay();
            });
        });

        render(0);
        startAutoplay();
    })();
    </script>
    @endpush
@else
    @php
        $heroNombre = strtoupper($empresa->nombre ?? 'Lavadora y Lubricadora');
        $heroPartes = explode(' ', $heroNombre);
        $heroInicio = implode(' ', array_slice($heroPartes, 0, 2));
        $heroDestacado = implode(' ', array_slice($heroPartes, 2));
    @endphp
    <section class="hero" id="inicio">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <div class="hero-eyebrow">ðŸš— {{ $empresa->eslogan_texto }}</div>
            <h1 class="hero-title">
                {{ $heroInicio ?: $heroNombre }}
                @if($heroDestacado)
                    <span class="accent">{{ $heroDestacado }}</span>
                @endif
            </h1>
            <p class="hero-sub">{{ $empresa->descripcion_corta_texto }}</p>
            <div class="hero-actions">
                <a href="#catalogo" class="btn-primary">Ver Catalogo</a>
                <a href="#contacto" class="btn-outline">Contactanos</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number stat-number-support">{{ $empresa->correo_contacto }}</div>
                    <div class="stat-label">Correo</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-number-support">{{ $empresa->ciudad_texto }}</div>
                    <div class="stat-label">Ciudad</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer">{{ $empresa->telefono_contacto }}</a>
                    </div>
                    <div class="stat-label">Llamanos</div>
                </div>
            </div>
        </div>
    </section>
@endif
