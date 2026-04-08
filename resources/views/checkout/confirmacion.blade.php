<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Pago Exitoso — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-block-size:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem; position:relative; overflow:hidden; }
        h1 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        /* Fondo decorativo */
        .bg-glow { position:fixed; inset:0; background:radial-gradient(ellipse 50% 50% at 50% 50%, rgba(40,167,69,0.08) 0%, transparent 70%); pointer-events:none; }

        .card { background:rgba(255,255,255,0.03); border:1px solid rgba(40,167,69,0.2); border-radius:16px; padding:2.5rem; max-inline-size:540px; inline-size:100%; text-align:center; position:relative; z-index:1; }

        /* Animación check */
        .success-icon { inline-size:80px; block-size:80px; border-radius:50%; background:rgba(40,167,69,0.1); border:2px solid rgba(40,167,69,0.4); display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 1.5rem; animation:popIn 0.5s ease; }
        @keyframes popIn { from{transform:scale(0.5);opacity:0} to{transform:scale(1);opacity:1} }

        .order-id { font-size:0.7rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:var(--muted); margin-block-end:0.5rem; }
        .confirm-title { font-size:2.8rem; color:white; margin-block-end:0.5rem; }
        .confirm-title span { color:#28a745; }
        .confirm-sub { font-size:0.85rem; color:var(--muted); margin-block-end:2rem; line-height:1.7; }

        .status-badge { display:inline-flex; align-items:center; gap:0.5rem; background:rgba(40,167,69,0.1); border:1px solid rgba(40,167,69,0.3); color:#4caf82; padding:0.5rem 1.25rem; border-radius:999px; font-size:0.78rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; margin-block-end:2rem; }

        /* Detalle de la orden */
        .order-detail { text-align:start; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:10px; overflow:hidden; margin-block-end:1.5rem; }
        .detail-header { padding:0.85rem 1.25rem; border-block-end:1px solid rgba(255,255,255,0.05); font-size:0.7rem; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; color:var(--muted); }
        .detail-row { display:flex; justify-content:space-between; padding:0.75rem 1.25rem; border-block-end:1px solid rgba(255,255,255,0.03); font-size:0.82rem; }
        .detail-row:last-child { border-block-end:none; }
        .detail-label { color:var(--muted); }
        .detail-value { color:white; font-weight:600; }

        /* Items */
        .items-list { text-align:start; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:10px; padding:1rem; margin-block-end:1.5rem; }
        .item-row { display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0; border-block-end:1px solid rgba(255,255,255,0.04); font-size:0.82rem; }
        .item-row:last-child { border-block-end:none; }
        .item-badge { font-size:0.65rem; background:rgba(255,255,255,0.06); border-radius:4px; padding:0.15rem 0.4rem; color:var(--muted); margin-inline-start:0.3rem; }
        .total-row { display:flex; justify-content:space-between; align-items:center; padding-block-start:0.75rem; border-block-start:1px solid rgba(255,255,255,0.07); margin-block-start:0.25rem; }
        .total-value { font-family:'Bebas Neue',sans-serif; font-size:1.8rem; color:#28a745; }

        /* Botones */
        .actions { display:flex; flex-direction:column; gap:0.75rem; }
        .btn-primary { background:var(--red); color:white; padding:0.9rem 2rem; border-radius:8px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; transition:all 0.2s; display:block; text-align:center; }
        .btn-primary:hover { background:var(--red-dark); }
        .btn-outline { background:transparent; color:rgba(255,255,255,0.6); border:1px solid rgba(255,255,255,0.1); padding:0.9rem 2rem; border-radius:8px; font-weight:600; font-size:0.78rem; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; transition:all 0.2s; display:block; text-align:center; }
        .btn-outline:hover { border-color:var(--gold); color:var(--gold); }

        .thanks-note { font-size:0.75rem; color:var(--muted); margin-block-start:1.5rem; line-height:1.6; }
        .thanks-note strong { color:rgba(255,255,255,0.5); }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<div class="card">

    {{-- Ícono éxito --}}
    <div class="success-icon">✅</div>

    <div class="order-id">Orden #{{ $order->id }}</div>
    <h1 class="confirm-title">¡PAGO <span>EXITOSO</span>!</h1>
    <p class="confirm-sub">
        Tu pago fue procesado correctamente.<br>
        Pronto recibirás atención de nuestro equipo.
    </p>

    <div class="status-badge">✅ Pago aprobado</div>

    {{-- Detalle de la orden --}}
    <div class="order-detail">
        <div class="detail-header">Detalle del pago</div>
        <div class="detail-row">
            <span class="detail-label">Cliente</span>
            <span class="detail-value">{{ $order->user->name ?? '—' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha</span>
            <span class="detail-value">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->transaction)
        <div class="detail-row">
            <span class="detail-label">Ref. Payphone</span>
            <span class="detail-value" style="font-family:monospace;font-size:0.75rem;">
                {{ $order->transaction->payphone_ref ?? '—' }}
            </span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Estado</span>
            <span class="detail-value" style="color:#28a745;">Pagado ✅</span>
        </div>
    </div>

    {{-- Items --}}
    <div class="items-list">
        @foreach($order->items as $item)
        <div class="item-row">
            <span>
                {{ $item->itemable->name ?? 'Ítem' }}
                <span class="item-badge">×{{ $item->quantity }}</span>
            </span>
            <span style="color:white;font-weight:600;">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
        </div>
        @endforeach
        <div class="total-row">
            <span style="font-size:0.82rem;font-weight:600;">Total pagado</span>
            <span class="total-value">${{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="actions">
        <a href="{{ route('cliente.compras') }}" class="btn-primary">
            📋 Ver mis compras
        </a>
        <a href="{{ route('home') }}" class="btn-outline">
            ← Volver al inicio
        </a>
    </div>

    <p class="thanks-note">
        ¡Gracias por confiar en <strong>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</strong>!<br>
        Tu vehículo quedará en las mejores manos. 🚗✨
    </p>

</div>

</body>
</html>