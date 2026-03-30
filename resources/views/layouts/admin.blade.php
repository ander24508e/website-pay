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

        /* ========== SIDEBAR (Desktop y móvil cuando está abierto) ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: var(--gray-900);
            color: white;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
            box-shadow: var(--shadow-xl);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-800);
        }

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-header p {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        .sidebar-nav {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--gray-100);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sidebar-nav a:hover {
            background: var(--gray-800);
        }

        .sidebar-nav a.active {
            background: var(--primary);
            color: white;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--gray-800);
            margin-top: auto;
        }

        .sidebar-footer button {
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            background: transparent;
            border: none;
            color: #f87171;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-footer button:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* ========== BOTTOM SHEET (Menú deslizable desde abajo) ========== */
        .bottom-sheet-trigger {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            z-index: 100;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bottom-sheet-trigger:hover {
            transform: scale(1.05);
            background: var(--primary-dark);
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
        }

        .bottom-sheet.open {
            transform: translateY(0);
        }

        /* Handle (barra para arrastrar) */
        .bottom-sheet-handle {
            display: flex;
            justify-content: center;
            padding: 12px 0 8px;
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

        .bottom-sheet-nav a:hover {
            background: var(--gray-100);
        }

        .bottom-sheet-nav a.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .bottom-sheet-nav a span:first-child {
            font-size: 1.25rem;
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
            transition: all 0.2s;
        }

        .bottom-sheet-footer button:hover {
            background: #fee2e2;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            padding: 1rem;
            min-height: 100vh;
        }

        /* Botón menú hamburguesa (solo móvil) */
        .menu-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 99;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
        }

        /* Overlay para sidebar */
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

        /* ========== RESPONSIVE ========== */
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
                position: fixed;
            }

            .menu-toggle,
            .bottom-sheet-trigger,
            .bottom-sheet-overlay,
            .bottom-sheet {
                display: none;
            }

            .main-content {
                margin-left: 280px;
                padding: 2rem;
            }

            .sidebar-overlay {
                display: none !important;
            }
        }

        @media (max-width: 767px) {
            .sidebar {
                width: 85%;
                max-width: 320px;
            }
        }

        /* Animaciones */
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        .sidebar.open {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>
<body>

    <!-- Botón menú hamburguesa (móvil) -->
    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú lateral">
        ☰
    </button>

    <!-- Overlay para sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar tradicional (Desktop y móvil cuando se abre) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1>⚡ Admin Panel</h1>
            <p>{{ auth()->user()?->name ?? 'Administrador' }}</p>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">🏷️ Categorías</a>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">📦 Productos</a>
            <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">🛠️ Servicios</a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">🧾 Órdenes</a>
            <a href="{{ route('admin.empresa.edit') }}" class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">🏢 Mi Empresa</a>
            <a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">💳 Transacciones</a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">🚪 Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="main-content">
        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- BOTÓN FLOTANTE PARA ABRIR BOTTOM SHEET (Móvil) -->
    <button class="bottom-sheet-trigger" id="bottomSheetTrigger">
        ☰
    </button>

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
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <span>🏷️</span>
                <span>Categorías</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span>📦</span>
                <span>Productos</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <span>🛠️</span>
                <span>Servicios</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <span>🧾</span>
                <span>Órdenes</span>
            </a>
            <a href="{{ route('admin.empresa.edit') }}" class="{{ request()->routeIs('admin.empresa.*') ? 'active' : '' }}">
                <span>🏢</span>
                <span>Mi Empresa</span>
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span>💳</span>
                <span>Transacciones</span>
            </a>
        </nav>

        <div class="bottom-sheet-footer">
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
        // ========== ELEMENTOS ==========
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        const bottomSheetTrigger = document.getElementById('bottomSheetTrigger');
        const bottomSheet = document.getElementById('bottomSheet');
        const bottomSheetOverlay = document.getElementById('bottomSheetOverlay');

        // ========== FUNCIONES SIDEBAR ==========
        function openSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle?.addEventListener('click', openSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);

        // Cerrar sidebar al hacer clic en enlaces (móvil)
        document.querySelectorAll('#sidebar a, #sidebar button').forEach(el => {
            el.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });

        // ========== FUNCIONES BOTTOM SHEET ==========
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
                if (sidebar.classList.contains('open')) closeSidebar();
                if (bottomSheet.classList.contains('open')) closeBottomSheet();
            }
        });

        // ========== DETECTAR SWIPE HACIA ARRIBA (para abrir bottom sheet) ==========
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', (e) => {
            touchEndY = e.changedTouches[0].clientY;
            const swipeDistance = touchEndY - touchStartY;
            
            // Si el usuario desliza hacia arriba desde la parte inferior (menos de 100px desde el borde)
            const isNearBottom = touchStartY > window.innerHeight - 100;
            
            if (isNearBottom && swipeDistance < -30 && !bottomSheet.classList.contains('open')) {
                openBottomSheet();
            }
        });

        // Cerrar con swipe hacia abajo dentro del bottom sheet
        const sheetHandle = document.querySelector('.bottom-sheet-handle');
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
                    closeSidebar();
                    closeBottomSheet();
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    </script>

    @stack('scripts')
</body>
</html>