<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Carrito — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/carrito.scss', 'resources/js/app.js'])
</head>

<body>

    {{-- Topbar --}}
    <header class="topbar">
        <a href="{{ route('home') }}" class="topbar-brand">{{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}</a>
        <nav class="topbar-nav">
            <a href="{{ route('home') }}" class="flex items-center gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
                Seguir comprando
            </a>
            @auth
                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}">Panel Admin</a>
                @else
                    <a href="{{ route('customer.compras') }}">Mis Compras</a>
                @endif
            @else
                <a href="{{ route('login') }}">Acceder</a>
            @endauth
        </nav>
    </header>

    <div class="container">

        <h1 class="page-title">MI <span>CARRITO</span></h1>
        <p class="page-sub">{{ count($carrito) }} {{ count($carrito) === 1 ? 'ítem' : 'ítems' }} en tu carrito</p>

        {{-- Alertas --}}
        @if (session('success'))
            <div class="alert alert-success flex items-center gap-2">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error flex items-center gap-2">
                <x-heroicon-o-x-circle class="w-5 h-5" />
                {{ session('error') }}
            </div>
        @endif

        @if (empty($carrito))
            {{-- Estado vacío --}}
            <div class="empty-state">
                <div class="empty-icon">
                    <x-heroicon-o-shopping-cart class="w-16 h-16 text-gray-400 mx-auto" />
                </div>
                <p class="empty-text">Tu carrito está vacío.<br>Agrega productos o servicios para continuar.</p>
                <a href="{{ route('home') }}#servicios"
                    style="background:var(--red);color:white;padding:0.85rem 2rem;border-radius:8px;font-weight:700;font-size:0.82rem;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;display:inline-block;transition:all 0.2s;">
                    Ver Servicios
                </a>
            </div>
        @else
            <div class="carrito-grid">

                {{-- Lista de items --}}
                <div class="items-card">
                    <div class="items-header">
                        <h3>Ítems seleccionados</h3>
                        <span style="font-size:0.75rem;color:var(--muted);">{{ count($carrito) }}
                            {{ count($carrito) === 1 ? 'ítem' : 'ítems' }}</span>
                    </div>

                    @foreach ($carrito as $key => $item)
                        <div class="item-row">
                            {{-- Imagen --}}
                            <div class="item-img">
                                @if ($item['image'])
                                    <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                                @else
                                    <x-heroicon-o-cube class="w-8 h-8 text-gray-400" />
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="item-info">
                                <div class="item-type">{{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}
                                </div>
                                <div class="item-name">{{ $item['name'] }}</div>
                                <div class="item-unit">${{ number_format($item['price'], 2) }} c/u</div>
                            </div>

                            {{-- Cantidad --}}
                            <div class="item-qty">
                                <span class="qty-val">{{ $item['quantity'] }}</span>
                            </div>

                            {{-- Precio total --}}
                            <div class="item-price">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>

                            {{-- Eliminar --}}
                            <form action="{{ route('carrito.quitar', $key) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn-remove" title="Eliminar">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- Resumen --}}
                <div class="resumen-card">
                    <div class="resumen-title">Resumen del Pedido</div>

                    @foreach ($carrito as $item)
                        <div class="resumen-row">
                            <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                            <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                        </div>
                    @endforeach

                    <div class="resumen-total">
                        <span class="resumen-total-label">Total</span>
                        <span class="resumen-total-value">${{ number_format($total, 2) }}</span>
                    </div>

                    {{-- Botón Proceder al Pago --}}
                    <a href="{{ route('checkout') }}" class="btn-checkout flex items-center justify-center gap-2"
                        style="margin: 0 auto;">
                        <x-heroicon-o-credit-card class="w-4 h-4 inline mr-1"/>
                        Proceder al Pago
                    </a>

                    {{-- Botón Vaciar carrito --}}
                    <form action="{{ route('carrito.limpiar') }}" method="POST"
                        onsubmit="return confirm('¿Vaciar todo el carrito?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-limpiar flex items-center justify-center gap-2 w-full"
                            style="margin: 0 auto;">
                            <x-heroicon-o-trash class="w-4 h-4 inline mr-1"/>
                            Vaciar carrito
                        </button>
                    </form>

                    {{-- Botón Seguir comprando (ahora fuera del formulario) --}}
                    <a href="{{ route('home') }}" class="btn-seguir flex items-center justify-center gap-1"
                        style="margin: 0 auto;">
                        <x-heroicon-o-arrow-left class="w-4 h-4 inline mr-1" />
                        Seguir comprando
                    </a>
                </div>

            </div>
        @endif

    </div>
</body>

</html>

