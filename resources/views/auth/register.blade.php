<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Crear Cuenta — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/scss/auth.scss', 'resources/js/app.js'])
</head>
<body class="auth-register">

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
        <div class="brand-sub">Crea tu cuenta</div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nombre --}}
        <div class="form-group">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="form-input" placeholder="Tu nombre" required autofocus>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-input" placeholder="tu@email.com" required>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div class="input-wrap">
                <input type="password" id="password" name="password"
                       class="form-input" placeholder="Mínimo 8 caracteres"
                       required oninput="checkStrength(this.value)">
                <button type="button" class="eye-btn" onclick="togglePass('password', 'eye1')">
                    <i id="eye1" class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div class="strength-bar" id="strength-bar"></div>
            <div class="strength-text" id="strength-text"></div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Confirmar Password --}}
        <div class="form-group">
            <label class="form-label">Confirmar Contraseña</label>
            <div class="input-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-input" placeholder="Repite tu contraseña" required>
                <button type="button" class="eye-btn" onclick="togglePass('password_confirmation', 'eye2')">
                    <i id="eye2" class="bi bi-eye-slash"></i>
                </button>
            </div>
            @error('password_confirmation')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Botón registrar --}}
        <button type="submit" class="btn-submit">
            <i class="bi bi-person-check"></i> Crear Cuenta
        </button>
    </form>

    <div class="divider"><span>¿Ya tienes cuenta?</span></div>

    <a href="{{ route('login') }}" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
    </a>

    <p class="terms">Al registrarte aceptas nuestros términos de servicio y política de privacidad.</p>
</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye-slash');
    icon.classList.toggle('bi-eye');
}

function checkStrength(val) {
    const bar  = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    bar.className = 'strength-bar';

    if (!val) { text.textContent = ''; return; }

    let score = 0;
    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    if (score <= 1) {
        bar.classList.add('weak');
        text.textContent = 'Contraseña débil';
        text.style.color = '#d82128';
    } else if (score <= 2) {
        bar.classList.add('medium');
        text.textContent = 'Contraseña moderada';
        text.style.color = '#f0b429';
    } else {
        bar.classList.add('strong');
        text.textContent = 'Contraseña fuerte';
        text.style.color = '#28a745';
    }
}
</script>
</body>
</html>