<style>
.navbar       { position:fixed; top:0; left:0; right:0; z-index:1000; display:flex; align-items:center; justify-content:space-between; padding:0 2.5rem; height:70px; background:rgba(10,10,10,0.92); backdrop-filter:blur(12px); border-bottom:1px solid rgba(216,33,40,0.2); }
.navbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.5rem; color:white; text-decoration:none; letter-spacing:0.08em; }
.navbar-brand span { color:var(--red); }
.navbar-links { display:flex; align-items:center; gap:2rem; list-style:none; }
.navbar-links a { color:rgba(255,255,255,0.75); text-decoration:none; font-size:0.8rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; transition:color 0.2s; }
.navbar-links a:hover { color:var(--gold); }
.btn-login    { background:var(--red)!important; color:white!important; padding:0.5rem 1.4rem; border-radius:4px; }
.btn-login:hover { background:var(--red-dark)!important; }
</style>

<nav class="navbar">
    <a href="#inicio" class="navbar-brand">ENDARA <span>CARWASH</span></a>
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