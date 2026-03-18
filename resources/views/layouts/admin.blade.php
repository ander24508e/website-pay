<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    {{-- Sidebar --}}
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-xl font-bold tracking-wide">⚡ Admin Panel</h1>
                <p class="text-gray-400 text-sm mt-1">{{ auth()->user()->name }}</p>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                    🏷️ Categorías
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                    📦 Productos
                </a>
                <a href="{{ route('admin.services.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.services.*') ? 'bg-gray-700' : '' }}">
                    🛠️ Servicios
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : '' }}">
                    🧾 Órdenes
                </a>
                <a href="{{ route('admin.transactions.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-gray-700 transition {{ request()->routeIs('admin.transactions.*') ? 'bg-gray-700' : '' }}">
                    💳 Transacciones
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-red-700 transition text-red-400">
                        🚪 Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido principal --}}
        <main class="ml-64 flex-1 p-8">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>