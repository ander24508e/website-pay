@php
    $waUrl = $empresa->whatsapp_url ?? '#';
@endphp

@php
    $landingBanners = $empresa->landingBanners->where('activo', true)->values();
    $heroNombre = strtoupper($empresa->nombre ?? 'Lavadora y Lubricadora');
    $heroPartes = explode(' ', $heroNombre);
    $heroInicio = implode(' ', array_slice($heroPartes, 0, 2));
    $heroDestacado = implode(' ', array_slice($heroPartes, 2));
    $totalSlides = 1 + $landingBanners->count();
@endphp

<section class="hero hero-carousel" id="inicio">
    <div class="hero-carousel-track" id="hero-carousel-track">
        <article class="hero-slide is-active" data-slide="0">
            <div class="hero-bg"></div>
            <div class="hero-slide-content hero-content">
                <div class="hero-eyebrow">Servicio destacado</div>
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
        </article>

        @foreach($landingBanners as $index => $banner)
            <article class="hero-slide" data-slide="{{ $index + 1 }}">
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

    @if($totalSlides > 1)
        <button type="button" class="hero-control hero-control-prev" id="hero-prev" aria-label="Banner anterior">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </button>
        <button type="button" class="hero-control hero-control-next" id="hero-next" aria-label="Banner siguiente">
            <x-heroicon-o-chevron-right class="w-6 h-6" />
        </button>

        <div class="hero-dots" id="hero-dots">
            @for($dot = 0; $dot < $totalSlides; $dot++)
                <button type="button"
                        class="hero-dot {{ $dot === 0 ? 'is-active' : '' }}"
                        data-dot="{{ $dot }}"
                        aria-label="Ir al banner {{ $dot + 1 }}"></button>
            @endfor
        </div>
    @endif
</section>

@if($totalSlides > 1)
    @push('scripts')
    <script>
    (() => {
        const track = document.getElementById('hero-carousel-track');
        const slides = Array.from(document.querySelectorAll('.hero-slide'));
        const dots = Array.from(document.querySelectorAll('.hero-dot'));
        const prev = document.getElementById('hero-prev');
        const next = document.getElementById('hero-next');

        if (!track || slides.length <= 1) return;

        let currentIndex = 0;
        let autoplay = null;
        let mobileScrollTick = null;

        const isMobile = () => window.matchMedia('(max-width: 768px)').matches;

        const updateDots = () => {
            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('is-active', dotIndex === currentIndex);
            });
        };

        const syncDesktopSlides = () => {
            slides.forEach((slide, slideIndex) => {
                slide.classList.toggle('is-active', slideIndex === currentIndex);
            });
            updateDots();
        };

        const scrollToMobileSlide = (index, behavior = 'smooth') => {
            const target = slides[index];
            if (!target) return;
            track.scrollTo({
                left: target.offsetLeft,
                behavior,
            });
            currentIndex = index;
            updateDots();
        };

        const render = (index, behavior = 'smooth') => {
            currentIndex = (index + slides.length) % slides.length;

            if (isMobile()) {
                scrollToMobileSlide(currentIndex, behavior);
                return;
            }

            syncDesktopSlides();
        };

        const detectClosestMobileSlide = () => {
            const trackLeft = track.scrollLeft;
            let closestIndex = 0;
            let closestDistance = Number.POSITIVE_INFINITY;

            slides.forEach((slide, index) => {
                const distance = Math.abs(slide.offsetLeft - trackLeft);
                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestIndex = index;
                }
            });

            if (closestIndex !== currentIndex) {
                currentIndex = closestIndex;
                updateDots();
            }
        };

        const startAutoplay = () => {
            clearInterval(autoplay);
            autoplay = setInterval(() => render(currentIndex + 1), 5000);
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

        track.addEventListener('scroll', () => {
            if (!isMobile()) return;
            window.clearTimeout(mobileScrollTick);
            mobileScrollTick = window.setTimeout(detectClosestMobileSlide, 60);
        }, { passive: true });

        window.addEventListener('resize', () => {
            render(currentIndex, 'auto');
        });

        render(0, 'auto');
        startAutoplay();
    })();
    </script>
    @endpush
@endif
