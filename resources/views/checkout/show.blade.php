<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Orden #{{ $order->id }} — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-block-size:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem; }
        h1,h2 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        .card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:16px; padding:2.5rem; max-inline-size:520px; inline-size:100%; text-align:center; }

        .order-icon { font-size:3rem; margin-block-end:1rem; }
        .order-id { font-size:0.7rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:var(--muted); margin-block-end:0.5rem; }
        .order-title { font-size:2.5rem; color:white; margin-block-end:0.5rem; }
        .order-title span { color:var(--gold); }
        .order-sub { font-size:0.85rem; color:var(--muted); margin-block-end:2rem; line-height:1.7; }

        .status-badge { display:inline-flex; align-items:center; gap:0.5rem; background:rgba(240,180,41,0.1); border:1px solid rgba(240,180,41,0.3); color:var(--gold); padding:0.5rem 1.25rem; border-radius:999px; font-size:0.78rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-block-end:2rem; }

        .items-list { text-align:start; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:10px; padding:1rem; margin-block-end:1.5rem; }
        .item-row { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-block-end:1px solid rgba(255,255,255,0.04); font-size:0.82rem; }
        .item-row:last-child { border-block-end:none; }
        .item-name { color:rgba(255,255,255,0.8); }
        .item-price { color:var(--red); font-weight:600; font-family:'Bebas Neue',sans-serif; font-size:1rem; }

        .total-row { display:flex; justify-content:space-between; align-items:center; padding-block-start:1rem; border-block-start:1px solid rgba(255,255,255,0.07); margin-block-start:0.5rem; }
        .total-label { font-size:0.82rem; font-weight:600; }
        .total-value { font-family:'Bebas Neue',sans-serif; font-size:2rem; color:var(--red); }

        .btn-primary { display:inline-block; background:var(--red); color:white; padding:0.9rem 2rem; border-radius:8px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; transition:all 0.2s; margin-block-start:1.5rem; }
        .btn-primary:hover { background:var(--red-dark); transform:translateY(-1px); }

        .btn-secondary { display:inline-block; color:var(--muted); font-size:0.78rem; text-decoration:none; margin-block-start:1rem; transition:color 0.2s; }
        .btn-secondary:hover { color:white; }
    </style>
</head>
<body>

<div class="card">
    <div class="order-icon">⏳</div>
    <div class="order-id">Orden #{{ $order->id }}</div>
    <h1 class="order-title">ORDEN <span>CREADA</span></h1>
    <p class="order-sub">
        Tu orden fue registrada correctamente.<br>
        Redirigiendo al proceso de pago con Payphone...
    </p>

    <div class="status-badge">
        ⏳ Pendiente de pago
    </div>

    {{-- Items --}}
    <div class="items-list">
        @foreach($order->items as $item)
        <div class="item-row">
            <span class="item-name">
                {{ $item->itemable->name ?? 'Ítem' }} ×{{ $item->quantity }}
            </span>
            <span class="item-price">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
        </div>
        @endforeach
        <div class="total-row">
            <span class="total-label">Total</span>
            <span class="total-value">${{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <p style="font-size:0.75rem;color:var(--muted);">
        Si no fuiste redirigido automáticamente, haz clic en el botón de abajo.
    </p>

    <a href="{{ route('checkout') }}" class="btn-primary">
        💳 Intentar pago de nuevo
    </a>
    <br>
    <a href="{{ route('home') }}" class="btn-secondary">← Volver al inicio</a>
</div>

</body>
</html>