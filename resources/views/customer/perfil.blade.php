<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — Endara Carwash</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-2:#141414; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-height:100vh; }
        h1,h2 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }
        .topbar { background:rgba(10,10,10,0.95); border-bottom:1px solid rgba(216,33,40,0.2); padding:0 2rem; height:70px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .topbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:white; text-decoration:none; }
        .topbar-brand span { color:var(--red); }
        .topbar-nav { display:flex; align-items:center; gap:1.5rem; }
        .topbar-nav a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.78rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; transition:color 0.2s; }
        .topbar-nav a:hover, .topbar-nav a.active { color:var(--gold); }
        .btn-logout { background:transparent; color:rgba(255,255,255,0.4); border:1px solid rgba(255,255,255,0.1); padding:0.4rem 1rem; border-radius:4px; font-size:0.72rem; font-weight:600; letter-spacing:0.1em; cursor:pointer; transition:all 0.2s; }
        .btn-logout:hover { border-color:var(--red); color:var(--red); }
        .container { max-width:600px; margin:0 auto; padding:3rem 2rem; }
        .page-title { font-size:2.5rem; color:white; margin-bottom:2rem; }
        .page-title span { color:var(--red); }
        .avatar-section { display:flex; align-items:center; gap:1.5rem; margin-bottom:2rem; }
        .avatar { width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid rgba(216,33,40,0.4); }
        .avatar-placeholder { width:80px; height:80px; border-radius:50%; background:rgba(216,33,40,0.1); border:2px solid rgba(216,33,40,0.3); display:flex; align-items:center; justify-content:center; font-size:1.8rem; }
        .card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:2rem; }
        .form-group { margin-bottom:1.25rem; }
        label { display:block; font-size:0.75rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-bottom:0.4rem; }
        input[type=text], input[type=email], input[type=password], input[type=file] { width:100%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:0.75rem 1rem; color:white; font-size:0.85rem; font-family:'Montserrat',sans-serif; transition:border-color 0.2s; }
        input:focus { outline:none; border-color:rgba(216,33,40,0.5); }
        .hint { font-size:0.72rem; color:var(--muted); margin-top:0.3rem; }
        .btn-save { background:var(--red); color:white; border:none; padding:0.85rem 2rem; border-radius:6px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; width:100%; margin-top:0.5rem; }
        .btn-save:hover { background:var(--red-dark); }
        .alert-success { background:rgba(40,167,69,0.1); border:1px solid rgba(40,167,69,0.3); color:#28a745; padding:0.75rem 1rem; border-radius:6px; font-size:0.82rem; margin-bottom:1.5rem; }
        .error { color:#ff6b6b; font-size:0.72rem; margin-top:0.3rem; }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('cliente.compras') }}">Mis Compras</a>
        <a href="{{ route('cliente.perfil') }}" class="active">Mi Perfil</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-logout">Salir</button>
        </form>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">MI <span>PERFIL</span></h1>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    {{-- Avatar --}}
    <div class="avatar-section">
        @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar">
        @else
            <div class="avatar-placeholder">👤</div>
        @endif
        <div>
            <p style="font-weight:600;font-size:0.95rem;">{{ $user->name }}</p>
            <p style="color:var(--muted);font-size:0.8rem;">{{ $user->email }}</p>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('cliente.perfil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
                <p class="hint">Mínimo 8 caracteres</p>
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña">
            </div>

            <div class="form-group">
                <label>Foto de Perfil</label>
                <input type="file" name="avatar" accept="image/*">
                <p class="hint">JPG, PNG o WEBP. Máximo 2MB.</p>
            </div>

            <button type="submit" class="btn-save">Guardar Cambios</button>
        </form>
    </div>
</div>

</body>
</html>