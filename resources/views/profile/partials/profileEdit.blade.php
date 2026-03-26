<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Mi Perfil — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --red: #d82128;
            --red-dark: #b41b21;
            --gold: #f0b429;
            --dark: #1e1e1e;
            --dark-2: #141414;
            --dark-3: #0a0a0a;
            --muted: #666666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-3);
            color: white;
            min-block-size: 100vh;
            overflow-x: hidden;
        }

        h1,
        h2 {
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 0.05em;
        }

        /* Fondo decorativo igual al login */
        .bg-layer {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 50% 60% at 80% 20%, rgba(216, 33, 40, 0.08) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 10% 80%, rgba(240, 180, 41, 0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 40px,
                    rgba(255, 255, 255, 0.01) 40px, rgba(255, 255, 255, 0.01) 41px);
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky;
            inset-block-start: 0;
            z-index: 100;
            background: rgba(10, 10, 10, 0.92);
            backdrop-filter: blur(12px);
            border-block-end: 1px solid rgba(216, 33, 40, 0.2);
            padding: 0 2.5rem;
            block-size: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            color: white;
            text-decoration: none;
            letter-spacing: 0.08em;
        }

        .topbar-brand span {
            color: var(--red);
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .topbar-nav a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .topbar-nav a:hover {
            color: var(--gold);
        }

        .topbar-nav a.active {
            color: white;
        }

        .btn-logout {
            background: transparent;
            color: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.4rem 1.1rem;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Montserrat', sans-serif;
        }

        .btn-logout:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* ── NOTIFICACIÓN ── */
        .notification {
            position: fixed;
            inset-block-start: 80px;
            inset-inline-end: 1.5rem;
            z-index: 999;
            padding: 0.85rem 1.25rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-inline-size: 280px;
            animation: slideIn 0.3s ease;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .notification.success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #4caf82;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-close {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 1rem;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px)
            }

            to {
                opacity: 1;
                transform: translateX(0)
            }
        }

        /* ── LAYOUT ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            max-inline-size: 720px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
        }

        /* ── PAGE HEADER ── */
        .page-eyebrow {
            display: inline-block;
            background: rgba(216, 33, 40, 0.12);
            border: 1px solid rgba(216, 33, 40, 0.35);
            color: var(--red);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 2px;
            margin-block-end: 0.75rem;
        }

        .page-title {
            font-size: 2.8rem;
            line-height: 1;
            color: white;
            margin-block-end: 0.3rem;
        }

        .page-title span {
            color: var(--red);
        }

        .page-sub {
            font-size: 0.82rem;
            color: var(--muted);
            margin-block-end: 2.5rem;
        }

        /* ── CARD ── */
        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .card-section {
            padding: 1.75rem 2rem;
            border-block-end: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card-section:last-child {
            border-block-end: none;
        }

        /* ── SECTION LABEL ── */
        .section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-block-end: 1.25rem;
        }

        /* ── AVATAR ── */
        .avatar-row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .avatar-img {
            inline-size: 90px;
            block-size: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(216, 33, 40, 0.4);
            flex-shrink: 0;
        }

        .avatar-info {
            flex: 1;
        }

        .avatar-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            color: white;
            line-height: 1;
        }

        .avatar-email {
            font-size: 0.78rem;
            color: var(--muted);
            margin-block-start: 0.2rem;
        }

        .file-name {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.4);
            margin: 0.5rem 0;
        }

        .btn-upload {
            background: var(--red);
            color: white;
            border: none;
            padding: 0.55rem 1.3rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: background 0.2s;
            font-family: 'Montserrat', sans-serif;
        }

        .btn-upload:hover {
            background: var(--red-dark);
        }

        .upload-hint {
            font-size: 0.68rem;
            color: var(--muted);
            margin-block-start: 0.4rem;
        }

        /* ── FORM FIELDS ── */
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.1rem;
        }

        @media(max-inline-size:560px) {
            .fields-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.67rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-block-end: 0.35rem;
        }

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 0.72rem 1rem;
            color: white;
            font-size: 0.84rem;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.2s;
            inline-size: 100%;
        }

        .form-input:focus {
            outline: none;
            border-color: rgba(216, 33, 40, 0.55);
            background: rgba(255, 255, 255, 0.07);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .form-input {
            padding-inline-end: 2.8rem;
        }

        .eye-btn {
            position: absolute;
            inset-inline-end: 0.8rem;
            inset-block-start: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .eye-btn:hover {
            color: white;
        }

        .field-error {
            color: #ff6b6b;
            font-size: 0.68rem;
            margin-block-start: 0.25rem;
        }

        /* ── SAVE BUTTON ── */
        .save-row {
            padding: 1.5rem 2rem;
        }

        .btn-save {
            background: var(--red);
            color: white;
            border: none;
            padding: 0.85rem 2.5rem;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-save:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(216, 33, 40, 0.3);
        }
    </style>
</head>

<body>

    <div class="bg-layer"></div>

    {{-- ── TOPBAR ── --}}
    <header class="topbar">
        <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
        <nav class="topbar-nav">
            <a href="{{ route('home') }}">Inicio</a>
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}">Panel Admin</a>
            @else
                <a href="{{ route('customer.compras') }}">Mis Compras</a>
                <a href="{{ route('profile.edit') }}" class="active">Mi Perfil</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-logout">Salir</button>
            </form>
        </nav>
    </header>

    {{-- ── NOTIFICACIÓN ── --}}
    @if (session('success'))
        <div class="notification success" id="notif">
            <div class="notification-content">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- ── CONTENIDO ── --}}
    <div class="page-wrap">

        <h1 class="page-title">MI <span>PERFIL</span></h1>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="card">

                {{-- ── FOTO DE PERFIL ── --}}
                <div class="card-section">
                    <div class="section-label">
                        <i class="bi bi-image"></i> Foto de Perfil
                    </div>
                    <div class="avatar-row">
                        <img src="{{ $user->foto_perfil_url }}" id="avatar-preview" alt="Foto de perfil"
                            class="avatar-img">
                        <div class="avatar-info">
                            <div class="avatar-name">{{ $user->name }}</div>
                            <div class="avatar-email">{{ $user->email }}</div>
                            <div class="file-name" id="file-name-display">
                                {{ $user->foto_perfil ? basename($user->foto_perfil) : 'Ningún archivo seleccionado' }}
                            </div>
                            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                                style="display:none;" onchange="previewAvatar(this)">
                            <button type="button" class="btn-upload"
                                onclick="document.getElementById('foto_perfil').click()">
                                <i class="bi bi-cloud-arrow-up"></i> Cambiar Foto
                            </button>
                            <p class="upload-hint">JPG, PNG — Máximo 4MB</p>
                            @error('foto_perfil')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── INFORMACIÓN PERSONAL ── --}}
                <div class="card-section">
                    <div class="section-label">
                        <i class="bi bi-person-vcard"></i> Información Personal
                    </div>
                    <div class="fields-grid">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="form-input" placeholder="Tu nombre" required>
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-input" placeholder="tu@email.com" required>
                            @error('email')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── CAMBIAR CONTRASEÑA ── --}}
                <div class="card-section">
                    <div class="section-label">
                        <i class="bi bi-shield-lock"></i> Cambiar Contraseña
                        <span
                            style="font-family:'Montserrat',sans-serif;font-size:0.62rem;color:var(--muted);text-transform:none;letter-spacing:0;font-weight:500;">—
                            Opcional</span>
                    </div>
                    <div class="fields-grid">
                        <div class="form-group">
                            <label class="form-label">Contraseña Actual</label>
                            <div class="input-wrap">
                                <input type="password" id="cur_pass" name="current_password" class="form-input"
                                    placeholder="Contraseña actual">
                                <button type="button" class="eye-btn" onclick="togglePass('cur_pass','eye1')">
                                    <i id="eye1" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <div class="input-wrap">
                                <input type="password" id="new_pass" name="password" class="form-input"
                                    placeholder="Mínimo 8 caracteres">
                                <button type="button" class="eye-btn" onclick="togglePass('new_pass','eye2')">
                                    <i id="eye2" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmar Contraseña</label>
                            <div class="input-wrap">
                                <input type="password" id="conf_pass" name="password_confirmation"
                                    class="form-input" placeholder="Repite la nueva contraseña">
                                <button type="button" class="eye-btn" onclick="togglePass('conf_pass','eye3')">
                                    <i id="eye3" class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── GUARDAR ── --}}
                <div class="save-row">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg"></i> Guardar Cambios
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        // Preview avatar
        function previewAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            if (file.size > 4 * 1024 * 1024) {
                alert('Imagen muy grande. Máximo 4MB.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
            const display = document.getElementById('file-name-display');
            display.textContent = file.name;
            display.style.color = 'var(--red)';
        }

        // Toggle password
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        }

        // Auto-ocultar notificación
        document.addEventListener('DOMContentLoaded', () => {
            const notif = document.getElementById('notif');
            if (notif) {
                setTimeout(() => {
                    notif.style.transition = 'opacity 0.4s';
                    notif.style.opacity = '0';
                    setTimeout(() => notif.remove(), 400);
                }, 4000);
            }
        });
    </script>
</body>

</html>
