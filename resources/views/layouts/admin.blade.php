<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/scss/admin/layout.scss', 'resources/js/app.js'])
</head>

<body>
    <!-- Tooltip flotante para feedback -->
    <div class="sidebar-tooltip" id="sidebarTooltip"></div>

    <!-- Overlay para sidebar móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar (Colapsable al hacer clic) -->
    <aside class="sidebar flex flex-col h-full" id="sidebar">
        <div class="sidebar-header">
            <h1>⚡ Admin Panel</h1>
            <p>{{ auth()->user()?->name ?? 'Administrador' }}</p>
        </div>

        <nav class="sidebar-nav flex-1">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <span><x-heroicon-o-globe-alt class="w-5 h-5" /></span>
                <span>Pagina web</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span><x-heroicon-o-chart-bar class="w-5 h-5" /></span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <span><x-heroicon-o-rectangle-group class="w-5 h-5" /></span>
                <span>Landing Page</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span><x-heroicon-o-tag class="w-5 h-5" /></span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('admin.products.index') }}"
                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span><x-heroicon-o-cube class="w-5 h-5" /></span>
                <span>Productos</span>
            </a>
            <a href="{{ route('admin.services.index') }}"
                class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <span><x-heroicon-o-wrench class="w-5 h-5" /></span>
                <span>Servicios</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span><x-heroicon-o-shopping-bag class="w-5 h-5" /></span>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}"
                class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span><x-heroicon-o-credit-card class="w-5 h-5" /></span>
                <span>Transacciones</span>
            </a>
            <!-- Mi Empresa dentro del nav, justo antes del footer -->
            <a href="{{ route('admin.empresa.edit') }}"
                class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span><x-heroicon-o-building-office class="w-5 h-5" /></span>
                <span>Mi Empresa</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <span><x-heroicon-o-user class="w-5 h-5" /></span>
                <span>Mi Perfil</span>
            </a>
        </nav>

        <div class="sidebar-footer mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-3 text-red-400">
                    <span><x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5" /></span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>
    <main class="main-content" id="mainContent">
        {{-- Toast Notifications - Aparecen y desaparecen automáticamente --}}
        @include('partials.toast-notifications')

        @yield('content')
    </main>


    <!-- Overlay difuminado para bottom sheet -->
    <div class="bottom-sheet-overlay" id="bottomSheetOverlay"></div>

    <!-- BOTTOM SHEET (Menú deslizable desde abajo) -->
    <div class="bottom-sheet" id="bottomSheet">
        <div class="bottom-sheet-handle">
            <span></span>
        </div>

        <div class="bottom-sheet-header">
            <h2>⚡ Panel de Control</h2>
            <p>{{ auth()->user()?->name ?? 'Administrador' }}</p>
        </div>

        <nav class="bottom-sheet-nav">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <span><x-heroicon-o-globe-alt class="w-5 h-5" /></span>
                <span>Pagina web</span>
            </a>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span><x-heroicon-o-chart-bar class="w-5 h-5" /></span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <span><x-heroicon-o-rectangle-group class="w-5 h-5" /></span>
                <span>Landing Page</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span><x-heroicon-o-tag class="w-5 h-5" /></span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('admin.products.index') }}"
                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span><x-heroicon-o-cube class="w-5 h-5" /></span>
                <span>Productos</span>
            </a>
            <a href="{{ route('admin.services.index') }}"
                class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <span><x-heroicon-o-wrench class="w-5 h-5" /></span>
                <span>Servicios</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span><x-heroicon-o-shopping-bag class="w-5 h-5" /></span>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('admin.empresa.edit') }}"
                class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span><x-heroicon-o-building-office class="w-5 h-5" /></span>
                <span>Mi Empresa</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}"
                class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span><x-heroicon-o-credit-card class="w-5 h-5" /></span>
                <span>Transacciones</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <span><x-heroicon-o-user class="w-5 h-5" /></span>
                <span>Mi Perfil</span>
            </a>
        </nav>

        <div class="bottom-sheet-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full">
                    <span><x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5" /></span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // ========== SIDEBAR COLLAPSE AL HACER CLIC (DESKTOP) ==========
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const tooltip = document.getElementById('sidebarTooltip');

        // Recuperar estado guardado
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed && window.innerWidth >= 768) {
            sidebar.classList.add('collapsed');
        }

        // Variable para controlar si el clic fue en un enlace
        let clickOnLink = false;

        // Detectar clics en enlaces para no colapsar
        document.querySelectorAll('#sidebar a, #sidebar button').forEach(el => {
            el.addEventListener('click', (e) => {
                clickOnLink = true;
                // En móvil, cerrar sidebar después del clic
                if (window.innerWidth < 768) {
                    closeMobileSidebar();
                }
                // Dejar que el enlace haga su trabajo
                setTimeout(() => {
                    clickOnLink = false;
                }, 100);
            });
        });

        // Colapsar/expandir al hacer clic en el sidebar (solo en desktop)
        sidebar.addEventListener('click', (e) => {
            // Solo en desktop (>= 768px)
            if (window.innerWidth >= 768) {
                // Si el clic fue en un enlace o botón, no colapsar
                if (clickOnLink) {
                    clickOnLink = false;
                    return;
                }

                // Colapsar/expandir
                sidebar.classList.toggle('collapsed');
                const collapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', collapsed);
            }
        });

        // Prevenir que los clics en elementos interactivos propaguen al sidebar
        document.querySelectorAll('#sidebar a, #sidebar button, #sidebar .sidebar-header').forEach(el => {
            el.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });

        // ========== SIDEBAR MÓVIL ==========
        const menuToggleMobile = document.getElementById('menuToggleMobile');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openMobileSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggleMobile?.addEventListener('click', openMobileSidebar);
        sidebarOverlay?.addEventListener('click', closeMobileSidebar);

        // ========== BOTTOM SHEET MÓVIL ==========
        const bottomSheetTrigger = document.getElementById('bottomSheetTrigger');
        const bottomSheet = document.getElementById('bottomSheet');
        const bottomSheetOverlay = document.getElementById('bottomSheetOverlay');

        function openBottomSheet() {
            bottomSheet.classList.add('open');
            bottomSheetOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeBottomSheet() {
            bottomSheet.classList.remove('open');
            bottomSheetOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        bottomSheetTrigger?.addEventListener('click', openBottomSheet);
        bottomSheetOverlay?.addEventListener('click', closeBottomSheet);

        // Cerrar bottom sheet al hacer clic en enlaces
        document.querySelectorAll('.bottom-sheet-nav a, .bottom-sheet-footer button').forEach(el => {
            el.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    closeBottomSheet();
                }
            });
        });

        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (sidebar.classList.contains('open')) closeMobileSidebar();
                if (bottomSheet.classList.contains('open')) closeBottomSheet();
            }
        });

        // ========== SWIPE PARA ABRIR BOTTOM SHEET ==========
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', (e) => {
            touchEndY = e.changedTouches[0].clientY;
            const swipeDistance = touchEndY - touchStartY;
            const isNearBottom = touchStartY > window.innerHeight - 100;

            if (isNearBottom && swipeDistance < -30 && !bottomSheet.classList.contains('open')) {
                openBottomSheet();
            }
        });

        // Cerrar con swipe hacia abajo
        let sheetStartY = 0;
        bottomSheet.addEventListener('touchstart', (e) => {
            sheetStartY = e.touches[0].clientY;
        });

        bottomSheet.addEventListener('touchmove', (e) => {
            const currentY = e.touches[0].clientY;
            const delta = currentY - sheetStartY;
            if (delta > 30 && bottomSheet.classList.contains('open')) {
                closeBottomSheet();
            }
        });

        // ========== RESIZE ==========
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth >= 768) {
                    closeMobileSidebar();
                    closeBottomSheet();
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    </script>

    @stack('scripts')
</body>

</html>
