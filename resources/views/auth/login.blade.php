<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $empresa = App\Models\Empresa::first() ?? new App\Models\Empresa();
        $primary = $empresa->color_primario_hex;
        $secondary = $empresa->color_secundario_hex;
        $tertiary = $empresa->color_terciario_hex;
        $darkenHex = function (string $hex, int $steps = 26): string {
            $hex = ltrim($hex, '#');
            $r = max(0, hexdec(substr($hex, 0, 2)) - $steps);
            $g = max(0, hexdec(substr($hex, 2, 2)) - $steps);
            $b = max(0, hexdec(substr($hex, 4, 2)) - $steps);
            return sprintf('#%02X%02X%02X', $r, $g, $b);
        };
    @endphp
    <title>Iniciar Sesion — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/scss/website.scss', 'resources/scss/auth.scss', 'resources/js/app.js'])
</head>
<body class="auth-login"
    data-brand-primary="{{ $primary }}"
    data-brand-primary-dark="{{ $darkenHex($primary) }}"
    data-brand-secondary="{{ $secondary }}"
    data-brand-tertiary="{{ $tertiary }}">

<div class="bg-layer"></div>

@include('website.navbar')

<main class="auth-page-wrap">
<div class="card">
    <div class="logo-wrap">
        <img src="{{ $empresa->logo_url ?? asset('Images/empresa-logo.jpg') }}"
             alt="{{ $empresa->nombre ?? 'Endara Carwash' }}"
             class="logo-img">
        <div class="brand">{{ strtoupper($empresa->nombre_corto ?? 'ENDARA CARWASH') }}</div>
        <div class="brand-sub">Accede a tu cuenta</div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" class="form-label" value="Correo Electronico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')"
                class="form-input" placeholder="tu@email.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="field-error mt-1" />
        </div>

        <div class="form-group">
            <x-input-label for="password" class="form-label" value="Contrasena" />
            <div class="input-wrap">
                <x-text-input id="password" type="password" name="password"
                    class="form-input" placeholder="********" required autocomplete="current-password" />
                <button type="button" class="eye-btn" onclick="togglePassword()">
                    <i id="eye-icon" class="bi bi-eye-slash"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="field-error mt-1" />
        </div>

        <div class="row-flex">
            <label class="remember">
                <input type="checkbox" name="remember">
                Recordarme
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
        </button>
    </form>

    <div class="divider"></div>

    <a href="{{ route('register') }}" class="btn-register">
        <i class="bi bi-person-plus"></i> Crear una cuenta
    </a>
</div>
</main>

<script>
function initAuthBrandVars() {
    const root = document.body;
    const primary = root.dataset.brandPrimary;
    const primaryDark = root.dataset.brandPrimaryDark;
    const secondary = root.dataset.brandSecondary;
    const tertiary = root.dataset.brandTertiary;
    if (!primary || !primaryDark || !secondary || !tertiary) return;

    root.style.setProperty('--brand-primary', primary);
    root.style.setProperty('--brand-primary-dark', primaryDark);
    root.style.setProperty('--brand-secondary', secondary);
    root.style.setProperty('--brand-tertiary', tertiary);
}

function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (!input || !icon) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye-slash');
    icon.classList.toggle('bi-eye');
}

document.addEventListener('DOMContentLoaded', initAuthBrandVars);
</script>
</body>
</html>
