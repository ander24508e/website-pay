<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/profile/profile-edit.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="bg-layer"></div>

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

    <main class="page-wrap">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form-grid">
            @csrf
            @method('PATCH')

            <section class="profile-left-stack">
                <article class="profile-card profile-card--avatar">
                    @include('profile.partials.form-avatar', ['user' => $user])
                </article>

                <article class="profile-card profile-card--info">
                    @include('profile.partials.form-account', ['user' => $user])
                </article>
            </section>

            <section class="profile-card profile-card--security">
                @include('profile.partials.form-security')

                <div class="save-row">
                    <button type="submit" class="btn-save">
                        <x-heroicon-o-check class="w-4 h-4" />
                        Guardar Cambios
                    </button>
                </div>
            </section>
        </form>
    </main>

    <script>
        function previewAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            if (file.size > 4 * 1024 * 1024) {
                alert('Imagen muy grande. Maximo 4MB.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
            const display = document.getElementById('file-name-display');
            if (display) {
                display.textContent = file.name;
                display.style.color = 'var(--red)';
            }
        }

        function togglePass(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        }

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
