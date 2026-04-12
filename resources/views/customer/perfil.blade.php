<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — Endara Carwash</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/profile/customer-perfil.scss', 'resources/js/app.js'])
</head>
<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('customer.compras') }}">Mis Compras</a>
        <a href="{{ route('customer.perfil') }}" class="active">Mi Perfil</a>
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
        <form action="{{ route('customer.perfil.update') }}" method="POST" enctype="multipart/form-data">
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
