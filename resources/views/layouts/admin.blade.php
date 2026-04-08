<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ========== VARIABLES ========== */
        :root {
            --primary: #d82128;
            --primary-dark: #b41b21;
            --primary-light: rgba(216, 33, 40, 0.1);
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-100: #f3f4f6;
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 72px;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--gray-100);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR (Colapsable al hacer clic) ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--gray-900);
            color: white;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: var(--shadow-xl);
            cursor: pointer;
        }

        /* Efecto hover al pasar por el sidebar */
        .sidebar:hover {
            background: var(--gray-800);
        }

        /* Sidebar colapsado */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Ocultar texto cuando está colapsado */
        .sidebar.collapsed .sidebar-nav a span:last-child,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .sidebar-footer button span:last-child {
            display: none;
        }

        /* Centrar íconos cuando está colapsado */
        .sidebar.collapsed .sidebar-nav a {
            justify-content: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .sidebar-header h1 {
            font-size: 0;
            text-align: center;
        }

        .sidebar.collapsed .sidebar-header h1::before {
            content: "⚡";
            font-size: 1.5rem;
        }

        .sidebar.collapsed .sidebar-footer button {
            justify-content: center;
            padding: 0.75rem;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--gray-800);
            transition: padding 0.3s;
            pointer-events: none;
        }

        .sidebar-header h1 {
            font-size: 1.1rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .sidebar-header p {
            font-size: 0.7rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
            white-space: nowrap;
        }

        .sidebar-nav {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            pointer-events: auto;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.875rem;
            border-radius: 0.5rem;
            color: var(--gray-100);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
            pointer-events: auto;
        }

        .sidebar-nav a:hover {
            background: var(--gray-700);
        }

        .sidebar-nav a.active {
            background: var(--primary);
            color: white;
        }

        .sidebar-nav a span:first-child {
            font-size: 1.2rem;
            min-width: 28px;
        }

        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid var(--gray-800);
            margin-top: auto;
            pointer-events: auto;
        }

        .sidebar-footer button {
            width: 100%;
            text-align: left;
            padding: 0.7rem 0.875rem;
            border-radius: 0.5rem;
            background: transparent;
            border: none;
            color: #f87171;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .sidebar-footer button:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Cuando sidebar está colapsado */
        .sidebar.collapsed~.main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* ========== BOTTOM SHEET (Móvil) ========== */
        .bottom-sheet-trigger {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            z-index: 100;
            transition: all 0.2s;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .bottom-sheet-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            backdrop-filter: blur(0px);
            z-index: 1000;
            display: none;
            transition: all 0.3s ease;
        }

        .bottom-sheet-overlay.active {
            display: block;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 24px 24px 0 0;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1001;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .bottom-sheet.open {
            transform: translateY(0);
        }

        .bottom-sheet-handle {
            display: flex;
            justify-content: center;
            padding: 12px 0 8px;
            cursor: grab;
        }

        .bottom-sheet-handle span {
            width: 40px;
            height: 4px;
            background: var(--gray-600);
            border-radius: 2px;
            opacity: 0.5;
        }

        .bottom-sheet-header {
            padding: 0 1.25rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .bottom-sheet-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .bottom-sheet-header p {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        .bottom-sheet-nav {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .bottom-sheet-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.75rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 1rem;
            font-weight: 500;
        }

        .bottom-sheet-nav a.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .bottom-sheet-footer {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            margin-top: 0.5rem;
        }

        .bottom-sheet-footer button {
            width: 100%;
            text-align: left;
            padding: 1rem;
            border-radius: 0.75rem;
            background: transparent;
            border: none;
            color: #dc2626;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1rem;
            font-weight: 500;
        }

        /* ========== RESPONSIVE ========== */
        @media (min-width: 768px) {

            .bottom-sheet-trigger,
            .bottom-sheet-overlay,
            .bottom-sheet {
                display: none !important;
            }
        }

        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
                width: 85%;
                max-width: 300px;
                transition: transform 0.3s ease;
                cursor: default;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar:hover {
                background: var(--gray-900);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .bottom-sheet-trigger {
                display: flex;
            }

            .bottom-sheet {
                display: block;
            }

            .menu-toggle-mobile {
                position: fixed;
                top: 1rem;
                left: 1rem;
                width: 44px;
                height: 44px;
                border-radius: 0.5rem;
                background: var(--primary);
                color: white;
                border: none;
                font-size: 1.25rem;
                cursor: pointer;
                z-index: 100;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: var(--shadow-lg);
            }
        }

        /* Overlay móvil */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Animaciones */
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .sidebar.open {
            animation: slideIn 0.3s ease-out;
        }

        /* Tooltip flotante al colapsar (feedback visual) */
        .sidebar-tooltip {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 2000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .sidebar-tooltip.show {
            opacity: 1;
        }
    </style>
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
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span>🏷️</span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('admin.products.index') }}"
                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span>📦</span>
                <span>Productos</span>
            </a>
            <a href="{{ route('admin.services.index') }}"
                class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <span>🛠️</span>
                <span>Servicios</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span>🧾</span>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}"
                class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span>💳</span>
                <span>Transacciones</span>
            </a>
            <!-- Mi Empresa dentro del nav, justo antes del footer -->
            <a href="{{ route('admin.empresa.edit') }}"
                class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span>🏢</span>
                <span>Mi Empresa</span>
            </a>
        </nav>

        <div class="sidebar-footer mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-3 text-red-400">
                    <span>🚪</span>
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
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span>🏷️</span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('admin.products.index') }}"
                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span>📦</span>
                <span>Productos</span>
            </a>
            <a href="{{ route('admin.services.index') }}"
                class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <span>🛠️</span>
                <span>Servicios</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span>🧾</span>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('admin.empresa.edit') }}"
                class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span>🏢</span>
                <span>Mi Empresa</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}"
                class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span>💳</span>
                <span>Transacciones</span>
            </a>
        </nav>

        <div class="bottom-sheet-footer">
            <a href="{{ route('admin.empresa.edit') }}"
                class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span>🏢</span>
                <span>Mi Empresa</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <span>🚪</span>
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
