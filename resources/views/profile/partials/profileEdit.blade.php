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
    @vite(['resources/css/app.css', 'resources/scss/profile/profile-edit.scss', 'resources/js/app.js'])
</head>

<body>

    <div class="bg-layer"></div>

    {{-- ── TOPBAR ── --}}
    <header class="topbar">
        <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
        <nav class="topbar-nav">
            <a href="{{ route('home') }}" class="flex items-center gap-1">
                <x-heroicon-o-home class="w-4 h-4" />
                Inicio
            </a>
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1">
                    <x-heroicon-o-chart-bar class="w-4 h-4" />
                    Panel Admin
                </a>
            @else
                <a href="{{ route('customer.compras') }}" class="flex items-center gap-1">
                    <x-heroicon-o-shopping-bag class="w-4 h-4" />
                    Mis Compras
                </a>
                <a href="{{ route('profile.edit') }}" class="active flex items-center gap-1">
                    <x-heroicon-o-user class="w-4 h-4" />
                    Mi Perfil
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-logout flex items-center gap-1">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" />
                    Salir
                </button>
            </form>
        </nav>
    </header>

    {{-- ── NOTIFICACIÓN ── --}}
    @if (session('success'))
        <div class="notification success" id="notif">
            <div class="notification-content">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                <span>{{ session('success') }}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <x-heroicon-o-x-mark class="w-4 h-4" />
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
                        <x-heroicon-o-photo class="w-4 h-4" />
                        Foto de Perfil
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
                            <button type="button" class="btn-upload" onclick="document.getElementById('foto_perfil').click()">
                                <x-heroicon-o-cloud-arrow-up class="w-4 h-4" />
                                Cambiar Foto
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
                        <x-heroicon-o-user class="w-4 h-4" />
                        Información Personal
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
                        <x-heroicon-o-shield-check class="w-4 h-4" />
                        Cambiar Contraseña
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
                                    <x-heroicon-o-eye-slash class="w-4 h-4" id="eye1" />
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
                                    <x-heroicon-o-eye-slash class="w-4 h-4" id="eye2" />
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
                                    <x-heroicon-o-eye-slash class="w-4 h-4" id="eye3" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── GUARDAR ── --}}
                <div class="save-row">
                    <button type="submit" class="btn-save">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Guardar Cambios
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
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            
            if (isPassword) {
                icon.outerHTML = `<x-heroicon-o-eye class="w-4 h-4" id="${iconId}" />`;
            } else {
                icon.outerHTML = `<x-heroicon-o-eye-slash class="w-4 h-4" id="${iconId}" />`;
            }
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