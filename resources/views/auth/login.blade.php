<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Iniciar Sesión — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/scss/auth.scss', 'resources/js/app.js'])
</head>
<body class="auth-login">

<div class="bg-layer"></div>

<a href="{{ route('home') }}" class="btn-back">
    <i class="bi bi-arrow-left"></i> Volver al inicio
</a>

<div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
        <img src="{{ $empresa->logo_url ?? asset('images/default-avatar.png') }}"
             alt="{{ $empresa->nombre ?? 'Endara Carwash' }}"
             class="logo-img">
        <div class="brand">ENDARA <span>CARWASH</span></div>
        <div class="brand-sub">Accede a tu cuenta</div>
    </div>

    {{-- Errores de sesión --}}
    @if(session('status'))
        <div style="background:rgba(40,167,69,0.1);border:1px solid rgba(40,167,69,0.3);color:#4caf82;padding:0.6rem 0.9rem;border-radius:6px;font-size:0.78rem;margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-input" placeholder="tu@email.com" required autofocus>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password"
                       class="form-input" placeholder="••••••••" required>
                <button type="button" class="eye-btn" onclick="togglePassword()">
                    <i id="eye-icon" class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Remember / Forgot --}}
        <div class="row-flex">
            <label class="remember">
                <input type="checkbox" name="remember">
                Recordarme
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        {{-- Botón login --}}
        <button type="submit" class="btn-submit">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </button>
    </form>

    <div class="divider"><span>o</span></div>

    {{-- Botón ir a register --}}
    <a href="{{ route('register') }}" class="btn-register">
        <i class="bi bi-person-plus"></i> Crear una cuenta
    </a>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye-slash');
    icon.classList.toggle('bi-eye');
}
</script>
</body>
</html>