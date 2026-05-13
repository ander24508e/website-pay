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
    <title>Crear Cuenta — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/scss/website.scss', 'resources/scss/auth.scss', 'resources/js/app.js'])
</head>
<body class="auth-register"
    style="
    --brand-primary: {{ $primary }};
    --brand-primary-dark: {{ $darkenHex($primary) }};
    --brand-secondary: {{ $secondary }};
    --brand-tertiary: {{ $tertiary }};
    --brand-action: var(--brand-primary);
    --brand-highlight: var(--brand-secondary);
    --brand-support: var(--brand-tertiary);
    --soft-primary: color-mix(in srgb, var(--brand-primary) 14%, transparent);
    --soft-highlight: color-mix(in srgb, var(--brand-secondary) 12%, transparent);
    --soft-support: color-mix(in srgb, var(--brand-tertiary) 14%, transparent);
    --line-support: color-mix(in srgb, var(--brand-tertiary) 24%, transparent);
    --red: var(--brand-primary);
    --red-dark: var(--brand-primary-dark);
    --gold: var(--brand-secondary);
">

<div class="bg-layer"></div>

@include('website.navbar')

<main class="auth-page-wrap">
<div class="card">
    <div class="logo-wrap">
        <img src="{{ $empresa->logo_url ?? asset('images/default-avatar.png') }}"
             alt="{{ $empresa->nombre ?? 'Endara Carwash' }}"
             class="logo-img">
        <div class="brand">{{ strtoupper($empresa->nombre_corto ?? 'ENDARA CARWASH') }}</div>
        <div class="brand-sub">Crea tu cuenta</div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-grid auth-form-grid-register">
            <div class="form-group">
                <x-input-label for="name" class="form-label" value="Nombre completo" />
                <x-text-input id="name" type="text" name="name" :value="old('name')"
                    class="form-input" placeholder="Tu nombre" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="field-error mt-1" />
            </div>

            <div class="form-group">
                <x-input-label for="email" class="form-label" value="Correo Electronico" />
                <x-text-input id="email" type="email" name="email" :value="old('email')"
                    class="form-input" placeholder="tu@email.com" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="field-error mt-1" />
            </div>

            <div class="form-group">
                <x-input-label for="password" class="form-label" value="Contrasena" />
                <div class="input-wrap">
                    <x-text-input id="password" type="password" name="password"
                        class="form-input" placeholder="Minimo 8 caracteres"
                        required autocomplete="new-password" oninput="checkStrength(this.value)" />
                    <button type="button" class="eye-btn" onclick="togglePass('password', 'eye1')" aria-label="Mostrar contraseña">
                        <i id="eye1" class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div class="strength-bar" id="strength-bar"></div>
                <div class="strength-text" id="strength-text"></div>
                <x-input-error :messages="$errors->get('password')" class="field-error mt-1" />
            </div>

            <div class="form-group">
                <x-input-label for="password_confirmation" class="form-label" value="Confirmar contrasena" />
                <div class="input-wrap">
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-input" placeholder="Repite tu contrasena" required autocomplete="new-password" />
                    <button type="button" class="eye-btn" onclick="togglePass('password_confirmation', 'eye2')" aria-label="Mostrar confirmación de contraseña">
                        <i id="eye2" class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="field-error mt-1" />
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="bi bi-person-check"></i> Crear Cuenta
        </button>
    </form>

    <div class="divider"><span>¿Ya tienes cuenta?</span></div>

    <a href="{{ route('login') }}" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion
    </a>
</div>
</main>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye-slash');
    icon.classList.toggle('bi-eye');
}

function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    if (!bar || !text) return;
    bar.className = 'strength-bar';

    if (!val) {
        text.textContent = '';
        return;
    }

    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    if (score <= 1) {
        bar.classList.add('weak');
        text.textContent = 'Contrasena debil';
        text.style.color = '#d82128';
    } else if (score <= 2) {
        bar.classList.add('medium');
        text.textContent = 'Contrasena moderada';
        text.style.color = '#f0b429';
    } else {
        bar.classList.add('strong');
        text.textContent = 'Contrasena fuerte';
        text.style.color = '#28a745';
    }
}
</script>

</body>
</html>
