<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/website.scss', 'resources/js/app.js'])
</head>
<body>

@include('website.navbar')
@include('website.hero')
@include('website.catalogo')
@include('website.contacto')
@include('website.footer')

{{-- Toast global --}}
<div id="toast" style="position:fixed;bottom:1.5rem;right:1rem;left:1rem;max-width:320px;margin:0 auto;background:#1e1e1e;border:1px solid rgba(216,33,40,0.4);color:white;padding:0.85rem 1.25rem;border-radius:8px;font-size:0.82rem;font-weight:600;display:none;z-index:9999;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
    ✅ Agregado al carrito
</div>

<script>
function slide(trackId, direction) {
    const track = document.getElementById(trackId);
    const card  = track.querySelector('.card');
    if (!card) return;
    const cardWidth = card.offsetWidth + 24;
    const current   = parseInt(track.dataset.offset || 0);
    const cards     = track.querySelectorAll('.card');
    const visible   = window.innerWidth <= 768 ? 1 : 3;
    const max       = Math.max(0, cards.length - visible);
    const newOffset = Math.max(0, Math.min(current + direction, max));
    track.dataset.offset = newOffset;
    track.style.transform = `translateX(-${newOffset * cardWidth}px)`;
}

function addToCart(id, type) {
    fetch('{{ route("carrito.agregar") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: JSON.stringify({ id, type, quantity: 1 })
    }).then(() => showToast('✅ Agregado al carrito'));
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

// Reset carrusel al cambiar tamaño de pantalla
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
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>
@stack('scripts')
</body>
</html>
