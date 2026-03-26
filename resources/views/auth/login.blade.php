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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --red:      #d82128;
            --red-dark: #b41b21;
            --gold:     #f0b429;
            --dark:     #1e1e1e;
            --dark-2:   #141414;
            --dark-3:   #0a0a0a;
            --muted:    #666666;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-3);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Fondo decorativo igual al hero */
        .bg-layer {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 60% at 70% 50%, rgba(216,33,40,0.1) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(240,180,41,0.05) 0%, transparent 60%);
            pointer-events: none;
        }
        .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(
                45deg, transparent, transparent 40px,
                rgba(255,255,255,0.01) 40px, rgba(255,255,255,0.01) 41px
            );
        }
        .bg-layer::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, transparent, var(--red), transparent);
        }

        /* Botón volver */
        .btn-back {
            position: fixed;
            top: 1.5rem; left: 1.5rem;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.2s;
            z-index: 10;
        }
        .btn-back:hover { color: var(--gold); }

        /* Card */
        .card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 2.5rem;
            backdrop-filter: blur(20px);
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* Logo */
        .logo-wrap {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-img {
            width: 80px; height: 80px;
            border-radius: 50%;
            object-fit: contain;
            border: 2px solid rgba(216,33,40,0.4);
            margin-bottom: 0.75rem;
        }
        .brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.6rem;
            letter-spacing: 0.06em;
            color: white;
            line-height: 1;
        }
        .brand span { color: var(--red); }
        .brand-sub {
            font-size: 0.72rem;
            color: var(--muted);
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        .form-label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }
        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.85rem;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: rgba(216,33,40,0.6);
            background: rgba(255,255,255,0.07);
        }
        .form-input::placeholder { color: rgba(255,255,255,0.2); }
        .input-wrap { position: relative; }
        .input-wrap .form-input { padding-right: 2.8rem; }
        .eye-btn {
            position: absolute;
            right: 0.85rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .eye-btn:hover { color: white; }

        /* Error */
        .field-error {
            color: #ff6b6b;
            font-size: 0.7rem;
            margin-top: 0.3rem;
            display: block;
        }

        /* Remember / Forgot */
        .row-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            margin-top: 0.25rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
        }
        .remember input { accent-color: var(--red); }
        .forgot {
            font-size: 0.72rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot:hover { color: var(--gold); }

        /* Botones */
        .btn-submit {
            width: 100%;
            background: var(--red);
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.85rem;
        }
        .btn-submit:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(216,33,40,0.35);
        }

        .btn-register {
            width: 100%;
            background: transparent;
            color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.12);
            padding: 0.9rem;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-register:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0.85rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.07);
        }
        .divider span {
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

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