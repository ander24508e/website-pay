<nav class="navbar">
    {{-- Brand --}}
    <a href="{{ route('home') }}" class="navbar-brand">
        <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre ?? 'Logo' }}" class="navbar-logo">
        @php
            $nombreEmpresa = strtoupper($empresa->nombre_corto ?? 'CARWASH');
            $partes = explode(' ', $nombreEmpresa);
            $inicio = implode(' ', array_slice($partes, 0, 2));
            $destacado = implode(' ', array_slice($partes, 2));
        @endphp
        <span class="navbar-brand-text">
            {{ $inicio ?: $nombreEmpresa }}
            @if($destacado)
                <span>{{ $destacado }}</span>
            @endif
        </span>
    </a>

    {{-- Desktop links --}}
    <ul class="navbar-links">
        <li><a href="{{ route('home') }}#inicio">Inicio</a></li>
        <li><a href="{{ route('home') }}#catalogo">Catalogo</a></li>
        <li><a href="{{ route('home') }}#contacto">Contacto</a></li>
        <li><a href="{{ route('carrito.index') }}">Carrito</a></li>

        @auth
            @if(auth()->user()->isStaff())
                <li><a href="{{ route('dashboard') }}" class="btn-login">Panel interno</a></li>
            @else
                <li><a href="{{ route('customer.compras') }}">Mis Compras</a></li>
                <li><a href="{{ route('profile.edit') }}">Mi Perfil</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-logout">Salir</button>
                    </form>
                </li>
            @endif
        @else
            <li><a href="{{ route('login') }}" class="btn-login">Acceder</a></li>
        @endauth
    </ul>

    {{-- Hamburger --}}
    <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

{{-- Mobile menu --}}
<ul class="mobile-menu" id="mobile-menu">
    <li><a href="{{ route('home') }}#inicio" onclick="closeMenu()">Inicio</a></li>
    <li><a href="{{ route('home') }}#catalogo" onclick="closeMenu()">Catalogo</a></li>
    <li><a href="{{ route('home') }}#contacto" onclick="closeMenu()">Contacto</a></li>
    <li><a href="{{ route('carrito.index') }}" onclick="closeMenu()">Carrito</a></li>
    @auth
        @if(auth()->user()->isStaff())
            <li><a href="{{ route('dashboard') }}" class="btn-login-mobile" onclick="closeMenu()">Panel interno</a></li>
        @else
            <li><a href="{{ route('customer.compras') }}" onclick="closeMenu()">Mis Compras</a></li>
            <li><a href="{{ route('profile.edit') }}" onclick="closeMenu()">Mi Perfil</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-logout-mobile">Cerrar sesion</button>
                </form>
            </li>
        @endif
    @else
        <li><a href="{{ route('login') }}" class="btn-login-mobile" onclick="closeMenu()">Acceder</a></li>
    @endauth
</ul>

<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');
const navSectionIds = ['inicio', 'catalogo', 'contacto'];

if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
        document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
    });
}

function closeMenu() {
    if (hamburger) hamburger.classList.remove('open');
    if (mobileMenu) mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
}

function setActiveNav(sectionId) {
    document.querySelectorAll('.navbar-links a, .mobile-menu a').forEach((link) => {
        const hash = link.hash ? link.hash.replace('#', '') : '';
        if (!hash) return;
        link.classList.toggle('is-active', hash === sectionId);
    });
}

function detectActiveSection() {
    const midpoint = window.scrollY + (window.innerHeight * 0.35);
    let current = 'inicio';

    navSectionIds.forEach((id) => {
        const section = document.getElementById(id);
        if (!section) return;
        if (midpoint >= section.offsetTop) current = id;
    });

    setActiveNav(current);
}

window.addEventListener('scroll', detectActiveSection, { passive: true });
window.addEventListener('hashchange', () => {
    const currentHash = (window.location.hash || '#inicio').replace('#', '');
    setActiveNav(currentHash);
});
window.addEventListener('load', () => {
    const currentHash = (window.location.hash || '#inicio').replace('#', '');
    setActiveNav(currentHash);
    detectActiveSection();
});
</script>
