<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Carrito — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Topbar --}}
<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">← Seguir comprando</a>
        @auth
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}">Panel Admin</a>
            @else
                <a href="{{ route('/customer.compras') }}">Mis Compras</a>
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
    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    @if(empty($carrito))
        {{-- Estado vacío --}}
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
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
                    <span style="font-size:0.75rem;color:var(--muted);">{{ count($carrito) }} {{ count($carrito) === 1 ? 'ítem' : 'ítems' }}</span>
                </div>

                @foreach($carrito as $key => $item)
                <div class="item-row">
                    {{-- Imagen --}}
                    <div class="item-img">
                        @if($item['image'])
                            <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                        @else
                            {{ $item['type'] === 'product' ? '📦' : '🛠️' }}
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="item-info">
                        <div class="item-type">{{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}</div>
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
                        <button class="btn-remove" title="Eliminar">✕</button>
                    </form>
                </div>
                @endforeach
            </div>

            {{-- Resumen --}}
            <div class="resumen-card">
                <div class="resumen-title">Resumen del Pedido</div>

                @foreach($carrito as $item)
                <div class="resumen-row">
                    <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                    <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                </div>
                @endforeach

                <div class="resumen-total">
                    <span class="resumen-total-label">Total</span>
                    <span class="resumen-total-value">${{ number_format($total, 2) }}</span>
                </div>

                @auth
                    <a href="{{ route('checkout') }}" class="btn-checkout">
                        💳 Proceder al Pago
                    </a>
                @else
                    <div class="auth-notice">
                        Debes <a href="{{ route('login') }}">iniciar sesión</a> para continuar con el pago.
                    </div>
                    <a href="{{ route('login') }}" class="btn-checkout" style="margin-block-start:0.75rem;">
                        Iniciar Sesión
                    </a>
                @endauth

                <form action="{{ route('carrito.limpiar') }}" method="POST"
                      onsubmit="return confirm('¿Vaciar todo el carrito?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn-limpiar">🗑 Vaciar carrito</button>
                </form>

                <a href="{{ route('home') }}" class="btn-seguir">← Seguir comprando</a>
            </div>

        </div>
    @endif

</div>
</body>

    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-2:#141414; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-block-size:100vh; }
        h1,h2 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        /* Topbar */
        .topbar { background:rgba(10,10,10,0.95); border-block-end:1px solid rgba(216,33,40,0.2); padding:0 2rem; block-size:70px; display:flex; align-items:center; justify-content:space-between; position:sticky; inset-block-start:0; z-index:100; }
        .topbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:white; text-decoration:none; letter-spacing:0.08em; }
        .topbar-brand span { color:var(--red); }
        .topbar-nav { display:flex; align-items:center; gap:1.5rem; }
        .topbar-nav a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.78rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; transition:color 0.2s; }
        .topbar-nav a:hover { color:var(--gold); }

        /* Container */
        .container { max-inline-size:1100px; margin:0 auto; padding:3rem 1.5rem; }
        .page-title { font-size:2.5rem; color:white; margin-block-end:0.3rem; }
        .page-title span { color:var(--red); }
        .page-sub { color:var(--muted); font-size:0.85rem; margin-block-end:2.5rem; }

        /* Alert */
        .alert { padding:0.85rem 1.25rem; border-radius:8px; font-size:0.82rem; font-weight:600; margin-block-end:1.5rem; }
        .alert-success { background:rgba(40,167,69,0.1); border:1px solid rgba(40,167,69,0.3); color:#4caf82; }
        .alert-error   { background:rgba(216,33,40,0.1); border:1px solid rgba(216,33,40,0.3); color:#ff6b6b; }

        /* Layout */
        .carrito-grid { display:grid; grid-template-columns:1fr 360px; gap:2rem; align-items:start; }
        @media(max-inline-size:768px){ .carrito-grid { grid-template-columns:1fr; } }

        /* Items */
        .items-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden; }
        .items-header { padding:1.25rem 1.5rem; border-block-end:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:space-between; }
        .items-header h3 { font-family:'Bebas Neue',sans-serif; font-size:1.2rem; letter-spacing:0.05em; }

        .item-row { display:flex; align-items:center; gap:1.25rem; padding:1.25rem 1.5rem; border-block-end:1px solid rgba(255,255,255,0.04); transition:background 0.2s; }
        .item-row:last-child { border-block-end:none; }
        .item-row:hover { background:rgba(255,255,255,0.02); }

        .item-img { inline-size:56px; block-size:56px; border-radius:8px; object-fit:cover; flex-shrink:0; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; font-size:1.5rem; overflow:hidden; }
        .item-img img { inline-size:100%; block-size:100%; object-fit:cover; }

        .item-info { flex:1; min-inline-size:0; }
        .item-type { font-size:0.62rem; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; color:var(--gold); margin-block-end:0.2rem; }
        .item-name { font-size:0.9rem; font-weight:600; color:white; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .item-unit  { font-size:0.75rem; color:var(--muted); margin-block-start:0.15rem; }

        .item-qty { display:flex; align-items:center; gap:0.5rem; }
        .qty-btn { inline-size:28px; block-size:28px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:white; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:0.9rem; transition:all 0.2s; }
        .qty-btn:hover { border-color:var(--red); background:rgba(216,33,40,0.1); }
        .qty-val { font-size:0.85rem; font-weight:600; min-inline-size:20px; text-align:center; }

        .item-price { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:var(--red); min-inline-size:80px; text-align:end; }

        .btn-remove { background:none; border:none; color:var(--muted); cursor:pointer; font-size:1rem; padding:0.25rem; transition:color 0.2s; }
        .btn-remove:hover { color:#ff6b6b; }

        /* Empty state */
        .empty-state { text-align:center; padding:4rem 2rem; }
        .empty-icon { font-size:3rem; margin-block-end:1rem; }
        .empty-text { color:var(--muted); font-size:0.9rem; margin-block-end:2rem; }

        /* Resumen */
        .resumen-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:1.5rem; position:sticky; inset-block-start:90px; }
        .resumen-title { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; margin-block-end:1.25rem; }
        .resumen-row { display:flex; justify-content:space-between; font-size:0.85rem; color:rgba(255,255,255,0.6); margin-block-end:0.75rem; }
        .resumen-total { display:flex; justify-content:space-between; align-items:center; padding-block-start:1rem; border-block-start:1px solid rgba(255,255,255,0.07); margin-block-start:0.5rem; }
        .resumen-total-label { font-size:0.85rem; font-weight:600; color:white; }
        .resumen-total-value { font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--red); }

        .btn-checkout { display:block; inline-size:100%; background:var(--red); color:white; border:none; padding:1rem; border-radius:8px; font-family:'Montserrat',sans-serif; font-weight:700; font-size:0.85rem; letter-spacing:0.1em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; text-align:center; text-decoration:none; margin-block-start:1.25rem; }
        .btn-checkout:hover { background:var(--red-dark); transform:translateY(-1px); box-shadow:0 8px 20px rgba(216,33,40,0.35); }

        .btn-limpiar { display:block; inline-size:100%; background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.08); padding:0.75rem; border-radius:8px; font-family:'Montserrat',sans-serif; font-weight:600; font-size:0.78rem; letter-spacing:0.08em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; text-align:center; margin-block-start:0.75rem; }
        .btn-limpiar:hover { border-color:rgba(216,33,40,0.4); color:#ff6b6b; }

        .btn-seguir { display:block; text-align:center; color:var(--muted); font-size:0.78rem; margin-block-start:1rem; text-decoration:none; transition:color 0.2s; }
        .btn-seguir:hover { color:var(--gold); }

        /* Auth notice */
        .auth-notice { background:rgba(240,180,41,0.08); border:1px solid rgba(240,180,41,0.25); border-radius:8px; padding:1rem 1.25rem; font-size:0.8rem; color:var(--gold); margin-block-start:1rem; text-align:center; }
        .auth-notice a { color:var(--gold); font-weight:700; text-decoration:underline; }
    </style>