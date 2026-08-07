<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    @php($primario = $empresa->color_primario_hex ?? '#D82128')
    @php($secundario = $empresa->color_secundario_hex ?? '#F0B429')
    @php($terciario = $empresa->color_terciario_hex ?? '#666666')
    <title>Carrito - {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --red: {{ $primario }};
            --red-dark: color-mix(in srgb, {{ $primario }} 82%, black);
            --gold: {{ $secundario }};
            --muted: {{ $terciario }};
        }
    </style>
    @vite(['resources/css/app.css', 'resources/scss/carrito.scss', 'resources/js/app.js'])
</head>

<body>

    {{-- Topbar --}}
    <header class="topbar">
        <a href="{{ route('home') }}" class="topbar-brand">{{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}</a>
        <nav class="topbar-nav">
            @auth
                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}">Panel de control</a>
                @else
                    <a href="{{ route('customer.compras') }}">Mis Compras</a>
                @endif
            @else
                <a href="{{ route('login') }}">Acceder</a>
            @endauth
        </nav>
        <button type="button" class="cart-menu-toggle" id="cartMenuToggle" aria-label="Abrir menu" aria-controls="cartMobileMenu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </header>

    <div class="cart-mobile-menu" id="cartMobileMenu" hidden>
        @auth
            @if (auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}">Panel de control</a>
            @else
                <a href="{{ route('customer.compras') }}">Mis Compras</a>
            @endif
        @else
            <a href="{{ route('login') }}">Acceder</a>
        @endauth
    </div>

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
            {{-- Estado vacio --}}
            <div class="empty-state">
                <div class="empty-icon">
                    <x-heroicon-o-shopping-cart class="w-16 h-16 text-gray-400 mx-auto" />
                </div>
                <p class="empty-text">Tu carrito está vacío.<br>¡Revisa nuestro catálogo!</p>
                <a href="{{ route('home') }}#catalogo"
                    style="background:var(--red);color:white;padding:0.85rem 2rem;border-radius:8px;font-weight:700;font-size:0.82rem;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;display:inline-block;transition:all 0.2s;">
                    Ver Catálogo
                </a>
            </div>
        @else
            <div class="carrito-grid">

                {{-- Lista de items --}}
                <div class="items-card">
                    <div class="items-header">
                        <div>
                            <h3>Items seleccionados</h3>
                            <span class="items-count">{{ count($carrito) }}
                                {{ count($carrito) === 1 ? 'Item' : 'Items' }}</span>
                        </div>

                        <form action="{{ route('carrito.limpiar') }}" method="POST"
                            onsubmit="return confirm('\u00bfVaciar todo el carrito?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-clear-cart" type="submit">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Vaciar carrito
                            </button>
                        </form>
                    </div>

                    <div class="cart-items-scroll" aria-label="Items seleccionados en el carrito">
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
                                <div class="item-type">{{ $item['type_label'] ?? ($item['type'] === 'product' ? 'Producto' : ($item['type'] === 'service' ? 'Servicio' : 'Catálogo')) }}
                                </div>
                                <div class="item-name">{{ $item['name'] }}</div>
                                @if (!empty($item['vehicle_label']) || !empty($item['vehicle_specification_label']) || !empty($item['vehicle_type_label']))
                                    <div class="item-unit">
                                        Vehiculo: {{ $item['vehicle_label'] ?? ($item['vehicle_specification_label'] ?? $item['vehicle_type_label']) }}
                                    </div>
                                @endif
                                <div class="item-unit">${{ number_format($item['price'], 2) }} c/u</div>
                            </div>

                            {{-- Cantidad --}}
                            <div class="item-qty">
                                <form action="{{ route('carrito.actualizar', $key) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ max(1, (int) $item['quantity'] - 1) }}">
                                    <button class="qty-btn" type="submit" aria-label="Restar cantidad"
                                        {{ (int) $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                                </form>

                                <span class="qty-val">{{ $item['quantity'] }}</span>

                                <form action="{{ route('carrito.actualizar', $key) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ (int) $item['quantity'] + 1 }}">
                                    <button class="qty-btn" type="submit" aria-label="Sumar cantidad">+</button>
                                </form>
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
                </div>

                {{-- Resumen --}}
                <div class="resumen-card">
                    <div class="resumen-title">Resumen del Pedido</div>

                    @foreach ($carrito as $item)
                        <div class="resumen-row">
                            <span>{{ $item['name'] }} {{ $item['quantity'] }}</span>
                            <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                        </div>
                    @endforeach

                    <div class="resumen-total">
                        <span class="resumen-total-label">Total</span>
                        <span class="resumen-total-value">${{ number_format($total, 2) }}</span>
                    </div>

                    <div class="resumen-actions">
                        <a href="{{ route('checkout') }}" class="btn-checkout">
                            <x-heroicon-o-credit-card class="w-4 h-4" />
                            Proceder al Pago
                        </a>


                        <a href="{{ route('home') }}#catalogo" class="btn-seguir">
                            <x-heroicon-o-arrow-left class="w-4 h-4" />
                            Seguir comprando
                        </a>
                    </div>
                </div>

            </div>
        @endif

    </div>

    @include('website.whatsapp-float')
    <script>
        (() => {
            const toggle = document.getElementById('cartMenuToggle');
            const menu = document.getElementById('cartMobileMenu');

            if (!toggle || !menu) return;

            toggle.addEventListener('click', () => {
                const isOpen = !menu.hasAttribute('hidden');

                if (isOpen) {
                    menu.setAttribute('hidden', '');
                    toggle.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                    return;
                }

                menu.removeAttribute('hidden');
                toggle.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            });
        })();
    </script>
</body>

</html>
