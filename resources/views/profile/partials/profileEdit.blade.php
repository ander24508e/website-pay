<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — Endara Carwash</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-2:#141414; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-height:100vh; }
        h1,h2 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        /* Topbar */
        .topbar { background:rgba(10,10,10,0.95); border-bottom:1px solid rgba(216,33,40,0.2); padding:0 2rem; height:70px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .topbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:white; text-decoration:none; }
        .topbar-brand span { color:var(--red); }
        .topbar-nav { display:flex; align-items:center; gap:1.5rem; }
        .topbar-nav a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.78rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; transition:color 0.2s; }
        .topbar-nav a:hover, .topbar-nav a.active { color:var(--gold); }
        .btn-logout { background:transparent; color:rgba(255,255,255,0.4); border:1px solid rgba(255,255,255,0.1); padding:0.4rem 1rem; border-radius:4px; font-size:0.72rem; font-weight:600; letter-spacing:0.1em; cursor:pointer; transition:all 0.2s; }
        .btn-logout:hover { border-color:var(--red); color:var(--red); }

        /* Notificación */
        .notification-success { position:fixed; top:80px; right:1.5rem; z-index:999; background:rgba(40,167,69,0.1); border:1px solid rgba(40,167,69,0.4); color:#4caf82; padding:0.85rem 1.25rem; border-radius:8px; display:flex; align-items:center; justify-content:space-between; gap:1rem; min-width:280px; animation:slideIn 0.3s ease; }
        .notification-content { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; font-weight:600; }
        .notification-close { background:none; border:none; color:inherit; cursor:pointer; font-size:1rem; }
        @keyframes slideIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }

        /* Layout */
        .container { max-width:700px; margin:0 auto; padding:3rem 2rem; }
        .card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden; }
        .profile-header { padding:2rem 2rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.05); }
        .profile-title { font-size:2rem; color:white; }
        .profile-title span { color:var(--red); }
        .profile-subtitle { color:var(--muted); font-size:0.82rem; margin-top:0.3rem; }
        .app-content { padding:2rem; }

        /* Secciones */
        .profile-section { margin-bottom:2rem; }
        .section-title { font-size:0.72rem; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:var(--gold); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; }
        .section-divider { border:none; border-top:1px solid rgba(255,255,255,0.05); margin:1.75rem 0; }

        /* Foto */
        .logo-preview-container { display:flex; align-items:center; gap:2rem; flex-wrap:wrap; }
        .empresa-logo-preview { width:100px; height:100px; object-fit:cover; border:2px solid rgba(216,33,40,0.3); border-radius:50%; margin-bottom:0.5rem; }
        .logo-current-text { font-size:0.75rem; color:var(--muted); }
        .logo-upload-section { flex:1; }
        #file-name-display { font-size:0.82rem; color:rgba(255,255,255,0.5); }
        .empresa-logo-hint { font-size:0.72rem; color:var(--muted); margin-top:0.5rem; }

        /* Campos */
        .fields-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
        @media(max-width:600px){ .fields-grid{grid-template-columns:1fr;} }
        .form-group { display:flex; flex-direction:column; }
        .form-label { font-size:0.7rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-bottom:0.4rem; }
        .form-input { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:0.75rem 1rem; color:white; font-size:0.85rem; font-family:'Montserrat',sans-serif; transition:border-color 0.2s; }
        .form-input:focus { outline:none; border-color:rgba(216,33,40,0.5); }
        .form-input::placeholder { color:rgba(255,255,255,0.2); }
        .error-message { color:#ff6b6b; font-size:0.72rem; margin-top:0.3rem; }

        /* Password toggle */
        .password-container { position:relative; }
        .password-field { padding-right:3rem; width:100%; }
        .password-toggle { position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; font-size:1rem; }
        .password-toggle:hover { color:white; }

        /* Botón guardar */
        .save-section { padding-top:1rem; }
        .btn-primary { background:var(--red); color:white; border:none; padding:0.85rem 2rem; border-radius:6px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:0.5rem; }
        .btn-primary:hover { background:var(--red-dark); transform:translateY(-1px); }
    </style>
</head>
<body>

{{-- Topbar según rol --}}
<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.dashboard') }}" class="active">Panel Admin</a>
        @else
            <a href="{{ route('cliente.compras') }}">Mis Compras</a>
            <a href="{{ route('profile.edit') }}" class="active">Mi Perfil</a>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-logout">Salir</button>
        </form>
    </nav>
</header>

{{-- Notificación --}}
@if(session('success'))
    <div class="notification-success">
        <div class="notification-content">
            <i class="bi bi-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    </div>
@endif

<div class="container">
    <div class="card">
        <div class="profile-header">
            <h1 class="profile-title">MI <span>PERFIL</span></h1>
            <p class="profile-subtitle">Administra tu información personal y configuración de seguridad</p>
        </div>
        <div class="app-content">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar notificación
    const notif = document.querySelector('.notification-success');
    if (notif) {
        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transition = 'opacity 0.3s';
            setTimeout(() => notif.remove(), 300);
        }, 4000);
    }
});
</script>
</body>
</html>