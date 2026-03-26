<style>
    .navbar {
        position: fixed;
        inset-block-start: 0;
        inset-inline-start: 0;
        inset-inline-end: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2.5rem;
        block-size: 70px;
        background: rgba(10, 10, 10, 0.92);
        backdrop-filter: blur(12px);
        border-block-end: 1px solid rgba(216, 33, 40, 0.2);
    }

    .navbar-brand {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 1.5rem;
        color: white;
        text-decoration: none;
        letter-spacing: 0.08em;
    }

    .navbar-brand span {
        color: var(--red);
    }

    .navbar-links {
        display: flex;
        align-items: center;
        gap: 2rem;
        list-style: none;
    }

    .navbar-links a {
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        transition: color 0.2s;
    }

    .navbar-links a:hover {
        color: var(--gold);
    }

    .btn-login {
        background: var(--red) !important;
        color: white !important;
        padding: 0.5rem 1.4rem;
        border-radius: 4px;
    }

    .btn-login:hover {
        background: var(--red-dark) !important;
    }

    .btn-logout {
        background: transparent;
        color: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 0.5rem 1.4rem;
        border-radius: 4px;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.1em !important;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-logout:hover {
        border-color: var(--red);
        color: var(--red) !important;
    }
</style>

<nav class="navbar">
    <a href="{{ route('home') }}" class="navbar-brand">ENDARA <span>CARWASH</span></a>

    <ul class="navbar-links">
        <li><a href="{{ route('home') }}#inicio">Inicio</a></li>
        <li><a href="{{ route('home') }}#servicios">Servicios</a></li>
        <li><a href="{{ route('home') }}#productos">Productos</a></li>
        <li><a href="{{ route('home') }}#contacto">Contacto</a></li>

        @auth
            @if (auth()->user()->hasRole('admin'))
                {{-- Navbar Admin --}}
                <li><a href="{{ route('admin.dashboard') }}" class="btn-login">⚡ Panel Admin</a></li>
            @else
                {{-- Navbar Cliente --}}
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
            {{-- Sin sesión --}}
            <li><a href="{{ route('login') }}" class="btn-login">Acceder</a></li>
        @endauth
    </ul>
</nav>
