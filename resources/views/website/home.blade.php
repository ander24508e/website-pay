<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</title>
    @php
        $primary = $empresa->color_primario_hex;
        $secondary = $empresa->color_secundario_hex;
        $tertiary = $empresa->color_terciario_hex;

        $darkenHex = function (string $hex, int $steps = 26): string {
            $hex = ltrim($hex, '#');
            $r = max(0, hexdec(substr($hex, 0, 2)) - $steps);
            $g = max(0, hexdec(substr($hex, 2, 2)) - $steps);
            $b = max(0, hexdec(substr($hex, 4, 2)) - $steps);
            return sprintf('#%02X%02X%02X', $r, $g, $b);
        };
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/website.scss', 'resources/js/app.js'])
</head>

<body
    style="
    --brand-primary: {{ $primary }};
    --brand-primary-dark: {{ $darkenHex($primary) }};
    --brand-secondary: {{ $secondary }};
    --brand-tertiary: {{ $tertiary }};
    --brand-action: var(--brand-primary);
    --brand-highlight: var(--brand-secondary);
    --brand-support: var(--brand-tertiary);
    --soft-primary: color-mix(in srgb, var(--brand-primary) 14%, transparent);
    --soft-highlight: color-mix(in srgb, var(--brand-secondary) 12%, transparent);
    --soft-support: color-mix(in srgb, var(--brand-tertiary) 14%, transparent);
    --line-support: color-mix(in srgb, var(--brand-tertiary) 24%, transparent);
    --red: var(--brand-primary);
    --red-dark: var(--brand-primary-dark);
    --gold: var(--brand-secondary);
">

    @include('website.navbar')
    @include('website.hero')
    @include('website.catalogo')
    @include('website.contacto')
    @include('website.footer')
    @include('website.whatsapp-float')
    @include('partials.website-notifications')

    <script>
        function slide(trackId, direction) {
            const track = document.getElementById(trackId);
            const card = track?.querySelector('.card');
            if (!card) return;
            const cardWidth = card.offsetWidth + 24;
            const current = parseInt(track.dataset.offset || 0);
            const cards = track.querySelectorAll('.card');
            const visible = window.innerWidth <= 768 ? 1 : 3;
            const max = Math.max(0, cards.length - visible);
            const newOffset = Math.max(0, Math.min(current + direction, max));
            track.dataset.offset = newOffset;
            track.style.transform = `translateX(-${newOffset * cardWidth}px)`;
        }

        function addToCart(id, type, quantity = 1, variantId = null) {
            fetch('{{ route('carrito.agregar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id, type, quantity, variant_id: variantId })
            }).then(() => window.websiteNotify?.('success', 'Agregado al carrito'));
        }

        function reserveService(serviceId) {
            fetch(`/reservas/servicio/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'No se pudo crear la reserva.');
                    window.websiteNotify?.('success', 'Reserva creada. Revisa tu orden.');
                })
                .catch((error) => {
                    window.websiteNotify?.('error', error.message || 'No se pudo crear la reserva.');
                });
        }

        function reserveItem(id, type) {
            if (type === 'service') {
                reserveService(id);
                return;
            }

            const contactoSection = document.getElementById('contacto');
            if (contactoSection) {
                contactoSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            window.websiteNotify?.('info', 'Puedes reservar este producto desde Contacto o WhatsApp.');
        }

        window.addEventListener('resize', () => {
            ['servicios-track', 'productos-track'].forEach(id => {
                const track = document.getElementById(id);
                if (track) {
                    track.dataset.offset = 0;
                    track.style.transform = 'translateX(0)';
                }
            });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('visible');
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>
    @stack('scripts')
</body>

</html>
