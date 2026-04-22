<nav class="navbar">
    {{-- Brand --}}
    <a href="{{ route('home') }}" class="navbar-brand">
        <img src="{{ $empresa->logo_url }}" alt="{{ $empresa->nombre ?? 'Logo' }}" class="navbar-logo">
        @php
            $nombreEmpresa = strtoupper($empresa->nombre_corto ?? 'CARWASH');
            $partes = explode(' ', $nombreEmpresa);
            $primera = array_shift($partes);
            $resto = implode(' ', $partes);
        @endphp
        <span class="navbar-brand-text">{{ $primera }} <span>{{ $resto }}</span></span>
    </a>

    {{-- Desktop links --}}
    <ul class="navbar-links">
        <li><a href="{{ route('home') }}#inicio">Inicio</a></li>
        <li><a href="{{ route('home') }}#catalogo">Catalogo</a></li>
        <li><a href="{{ route('home') }}#contacto">Contacto</a></li>
        <li><a href="{{ route('carrito.index') }}">Carrito</a></li>

        @auth
            @if(auth()->user()->hasRole('admin'))
                <li><a href="{{ route('admin.dashboard') }}" class="btn-login">Panel Admin</a></li>
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
        @if(auth()->user()->hasRole('admin'))
            <li><a href="{{ route('admin.dashboard') }}" class="btn-login-mobile" onclick="closeMenu()">Panel Admin</a></li>
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
</script>
