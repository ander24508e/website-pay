<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Checkout — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-2:#141414; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-block-size:100vh; }
        h1,h2 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        .topbar { background:rgba(10,10,10,0.95); border-block-end:1px solid rgba(216,33,40,0.2); padding:0 2rem; block-size:70px; display:flex; align-items:center; justify-content:space-between; position:sticky; inset-block-start:0; z-index:100; }
        .topbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:white; text-decoration:none; }
        .topbar-brand span { color:var(--red); }

        /* Pasos */
        .steps { display:flex; align-items:center; gap:0.5rem; }
        .step { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); }
        .step.active { color:white; }
        .step.done { color:var(--gold); }
        .step-sep { color:rgba(255,255,255,0.2); font-size:0.7rem; }

        .container { max-inline-size:900px; margin:0 auto; padding:3rem 1.5rem; }
        .page-title { font-size:2.5rem; color:white; margin-block-end:0.3rem; }
        .page-title span { color:var(--red); }
        .page-sub { color:var(--muted); font-size:0.85rem; margin-block-end:2.5rem; }

        .checkout-grid { display:grid; grid-template-columns:1fr 340px; gap:2rem; }
        @media(max-inline-size:768px){ .checkout-grid { grid-template-columns:1fr; } }

        /* Card general */
        .card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; overflow:hidden; margin-block-end:1.5rem; }
        .card-header { padding:1.25rem 1.5rem; border-block-end:1px solid rgba(255,255,255,0.05); }
        .card-header h3 { font-family:'Bebas Neue',sans-serif; font-size:1.1rem; letter-spacing:0.05em; }
        .card-body { padding:1.5rem; }

        /* Datos del cliente */
        .cliente-row { display:flex; align-items:center; gap:1rem; }
        .cliente-avatar { inline-size:48px; block-size:48px; border-radius:50%; background:rgba(216,33,40,0.1); border:2px solid rgba(216,33,40,0.3); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
        .cliente-name { font-weight:600; color:white; }
        .cliente-email { font-size:0.78rem; color:var(--muted); margin-block-start:0.1rem; }

        /* Items */
        .item-row { display:flex; align-items:center; gap:1rem; padding:0.85rem 0; border-block-end:1px solid rgba(255,255,255,0.04); }
        .item-row:last-child { border-block-end:none; }
        .item-emoji { inline-size:40px; block-size:40px; background:rgba(255,255,255,0.04); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
        .item-info { flex:1; }
        .item-name { font-size:0.85rem; font-weight:600; color:white; }
        .item-qty  { font-size:0.75rem; color:var(--muted); }
        .item-price { font-family:'Bebas Neue',sans-serif; font-size:1.2rem; color:var(--red); }

        /* Resumen lateral */
        .resumen-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:1.5rem; position:sticky; inset-block-start:90px; }
        .resumen-title { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; margin-block-end:1.25rem; }
        .resumen-row { display:flex; justify-content:space-between; font-size:0.82rem; color:rgba(255,255,255,0.6); margin-block-end:0.6rem; }
        .resumen-divider { border:none; border-block-start:1px solid rgba(255,255,255,0.07); margin:1rem 0; }
        .resumen-total { display:flex; justify-content:space-between; align-items:center; }
        .resumen-total-label { font-size:0.85rem; font-weight:600; }
        .resumen-total-value { font-family:'Bebas Neue',sans-serif; font-size:2.2rem; color:var(--red); }

        .payphone-badge { display:flex; align-items:center; gap:0.5rem; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:0.75rem 1rem; margin:1.25rem 0; }
        .payphone-badge span { font-size:0.75rem; color:var(--muted); }
        .payphone-badge strong { color:white; }

        .btn-pay { display:block; inline-size:100%; background:var(--red); color:white; border:none; padding:1.1rem; border-radius:8px; font-family:'Montserrat',sans-serif; font-weight:700; font-size:0.88rem; letter-spacing:0.1em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; text-align:center; }
        .btn-pay:hover { background:var(--red-dark); transform:translateY(-1px); box-shadow:0 8px 24px rgba(216,33,40,0.4); }
        .btn-pay:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

        .btn-back { display:block; text-align:center; color:var(--muted); font-size:0.78rem; margin-block-start:1rem; text-decoration:none; transition:color 0.2s; }
        .btn-back:hover { color:white; }

        .secure-note { text-align:center; font-size:0.7rem; color:var(--muted); margin-block-start:1rem; }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <div class="steps">
        <span class="step done">Carrito</span>
        <span class="step-sep">›</span>
        <span class="step active">Resumen</span>
        <span class="step-sep">›</span>
        <span class="step">Pago</span>
        <span class="step-sep">›</span>
        <span class="step">Confirmación</span>
    </div>
</header>

<div class="container">
    <h1 class="page-title">RESUMEN DEL <span>PEDIDO</span></h1>
    <p class="page-sub">Revisa tu pedido antes de proceder al pago</p>

    <div class="checkout-grid">

        {{-- Columna izquierda --}}
        <div>
            {{-- Datos del cliente --}}
            <div class="card">
                <div class="card-header"><h3>Datos del cliente</h3></div>
                <div class="card-body">
                    <div class="cliente-row">
                        <div class="cliente-avatar">👤</div>
                        <div>
                            <div class="cliente-name">{{ auth()->user()->name }}</div>
                            <div class="cliente-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ítems --}}
            <div class="card">
                <div class="card-header">
                    <h3>Ítems del pedido ({{ count($carrito) }})</h3>
                </div>
                <div class="card-body">
                    @foreach($carrito as $item)
                    <div class="item-row">
                        <div class="item-emoji">
                            {{ $item['type'] === 'product' ? '📦' : '🛠️' }}
                        </div>
                        <div class="item-info">
                            <div class="item-name">{{ $item['name'] }}</div>
                            <div class="item-qty">
                                {{ $item['type'] === 'product' ? 'Producto' : 'Servicio' }}
                                × {{ $item['quantity'] }}
                            </div>
                        </div>
                        <div class="item-price">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Resumen lateral --}}
        <div class="resumen-card">
            <div class="resumen-title">Total a Pagar</div>

            @foreach($carrito as $item)
            <div class="resumen-row">
                <span>{{ Str::limit($item['name'], 22) }} ×{{ $item['quantity'] }}</span>
                <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
            </div>
            @endforeach

            <hr class="resumen-divider">

            <div class="resumen-total">
                <span class="resumen-total-label">Total</span>
                <span class="resumen-total-value">${{ number_format($total, 2) }}</span>
            </div>

            {{-- Badge Payphone --}}
            <div class="payphone-badge">
                <span>💳</span>
                <div>
                    <strong>Pago seguro con Payphone</strong><br>
                    <span>Tarjeta de crédito o débito</span>
                </div>
            </div>

            {{-- Botón pagar --}}
            <form action="{{ route('orden.store') }}" method="POST" id="checkout-form">
                @csrf
                <button type="submit" class="btn-pay" id="pay-btn"
                        onclick="this.disabled=true; this.textContent='Procesando...'; this.form.submit();">
                    💳 Pagar ${{ number_format($total, 2) }}
                </button>
            </form>

            <a href="{{ route('carrito.index') }}" class="btn-back">← Volver al carrito</a>

            <p class="secure-note">🔒 Pago 100% seguro procesado por Payphone</p>
        </div>

    </div>
</div>
</body>
</html>