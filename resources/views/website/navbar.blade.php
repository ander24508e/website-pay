<style>
    .navbar {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2.5rem;
        height: 70px;
        background: rgba(10,10,10,0.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(216,33,40,0.2);
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        z-index: 1001;
    }

    .navbar-logo {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        object-fit: contain;
        border: 1px solid rgba(216,33,40,0.3);
    }

    .navbar-brand-text {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.4rem;
        color: white;
        letter-spacing: 0.08em;
        line-height: 1;
    }
    .navbar-brand-text span { color: var(--red); }

    /* Links desktop */
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
        background: var(--red) !important;
        color: white !important;
        padding: 0.5rem 1.4rem;
        border-radius: 4px;
    }
    .btn-login:hover { background: var(--red-dark) !important; }

    .btn-logout {
        background: transparent;
        color: rgba(255,255,255,0.5) !important;
        border: 1px solid rgba(255,255,255,0.15);
        padding: 0.5rem 1.4rem;
        border-radius: 4px;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.1em !important;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Montserrat', sans-serif;
    }
    .btn-logout:hover { border-color: var(--red); color: var(--red) !important; }

    /* Hamburger */
    .hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0.5rem;
        z-index: 1001;
    }
    .hamburger span {
        display: block;
        width: 24px;
        height: 2px;
        background: white;
        border-radius: 2px;
        transition: all 0.3s;
    }
    .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .hamburger.open span:nth-child(2) { opacity: 0; }
    .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    /* Mobile menu overlay */
    .mobile-menu {
        display: none;
        position: fixed;
        top: 70px; left: 0; right: 0; bottom: 0;
        background: rgba(5,5,5,0.98);
        backdrop-filter: blur(20px);
        z-index: 999;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        padding: 2rem;
        border-top: 1px solid rgba(216,33,40,0.2);
    }
    .mobile-menu.open { display: flex; }

    .mobile-menu li {
        list-style: none;
        width: 100%;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .mobile-menu li:last-child { border-bottom: none; }

    .mobile-menu a,
    .mobile-menu button {
        display: block;
        width: 100%;
        padding: 1.1rem 0;
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        transition: color 0.2s;
        background: none;
        border: none;
        cursor: pointer;
        font-family: 'Montserrat', sans-serif;
    }
    .mobile-menu a:hover,
    .mobile-menu button:hover { color: var(--gold); }

    .mobile-menu .btn-login-mobile {
        margin-top: 1.5rem;
        background: var(--red);
        color: white;
        padding: 0.9rem 2rem;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        transition: background 0.2s;
        width: auto;
    }
    .mobile-menu .btn-login-mobile:hover { background: var(--red-dark); color: white; }

    @media (max-width: 768px) {
        .navbar { padding: 0 1.25rem; }
        .navbar-links { display: none; }
        .hamburger { display: flex; }
        .navbar-brand-text { font-size: 1.1rem; }
        .navbar-logo { width: 44px; height: 44px; }
    }
</style>

<nav class="navbar">
    {{-- Brand --}}
    <a href="{{ route('home') }}" class="navbar-brand">
        @if($empresa && $empresa->logo)
            <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre ?? 'Logo' }}" class="navbar-logo">
        @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="Logo" class="navbar-logo">
        @endif
        @php
            $nombreEmpresa = strtoupper($empresa->nombre ?? 'Endara Carwash');
            $partes = explode(' ', $nombreEmpresa);
            $primera = array_shift($partes);
            $resto = implode(' ', $partes);
        @endphp
        <span class="navbar-brand-text">{{ $primera }} <span>{{ $resto }}</span></span>
    </a>

    {{-- Desktop links --}}
    <ul class="navbar-links">
        <li><a href="{{ route('home') }}#inicio">Inicio</a></li>
        <li><a href="{{ route('home') }}#servicios">Servicios</a></li>
        <li><a href="{{ route('home') }}#productos">Productos</a></li>
        <li><a href="{{ route('home') }}#contacto">Contacto</a></li>
        <li><a href="{{ route('home') }}/carrito">Carrito</a></li>

        @auth
            @if(auth()->user()->hasRole('admin'))
                <li><a href="{{ route('admin.dashboard') }}" class="btn-login">⚡ Panel Admin</a></li>
            @else
                <li><a href="{{ route('/customer.compras') }}">Mis Compras</a></li>
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
    <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
</nav>

{{-- Mobile menu --}}
<ul class="mobile-menu" id="mobile-menu">
    <li><a href="{{ route('home') }}#inicio" onclick="closeMenu()">Inicio</a></li>
    <li><a href="{{ route('home') }}#servicios" onclick="closeMenu()">Servicios</a></li>
    <li><a href="{{ route('home') }}#productos" onclick="closeMenu()">Productos</a></li>
    <li><a href="{{ route('home') }}#contacto" onclick="closeMenu()">Contacto</a></li>}
    <li><a href="{{ route('home') }}/carrito">Carrito</a></li>
    @auth
        @if(auth()->user()->hasRole('admin'))
            <li><a href="{{ route('admin.dashboard') }}" class="btn-login-mobile">⚡ Panel Admin</a></li>
        @else
            <li><a href="{{ route('/customer.compras') }}" onclick="closeMenu()">Mis Compras</a></li>
            <li><a href="{{ route('profile.edit') }}" onclick="closeMenu()">Mi Perfil</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button style="color:rgba(216,33,40,0.8);">Cerrar sesión</button>
                </form>
            </li>
        @endif
    @else
        <li><a href="{{ route('login') }}" class="btn-login-mobile">Acceder</a></li>
    @endauth
</ul>

<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mobileMenu.classList.toggle('open');
    document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
});

function closeMenu() {
    hamburger.classList.remove('open');
    mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
}
</script>