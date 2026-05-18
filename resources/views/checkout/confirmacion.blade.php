<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Pago Exitoso — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- QR Code library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])

    <style>
        /* ── Variables ─────────────────────────────────────────── */
        :root {
            --green:      #22c55e;
            --green-dark: #16a34a;
            --bg:         #0f0f0f;
            --card-bg:    #1a1a1a;
            --border:     rgba(255,255,255,0.08);
            --muted:      #6b7280;
            --text:       #f3f4f6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            font-family: 'Montserrat', sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 1rem 4rem;
        }

        /* ── Topbar ─────────────────────────────────────────────── */
        .topbar {
            width: 100%;
            max-width: 680px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .topbar-brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            color: var(--text);
            text-decoration: none;
            letter-spacing: 0.08em;
        }
        .topbar-brand span { color: var(--green); }

        /* ── Card principal ─────────────────────────────────────── */
        .confirm-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
        }

        /* ── Ícono éxito ────────────────────────────────────────── */
        .success-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(34,197,94,0.12);
            border: 2px solid var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            animation: pop 0.4s ease;
        }
        @keyframes pop {
            0%   { transform: scale(0.6); opacity: 0; }
            80%  { transform: scale(1.1); }
            100% { transform: scale(1);   opacity: 1; }
        }

        /* ── Títulos ────────────────────────────────────────────── */
        .order-badge {
            background: rgba(34,197,94,0.1);
            color: var(--green);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 999px;
            padding: 0.3rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .confirm-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 2.4rem;
            letter-spacing: 0.06em;
            text-align: center;
            line-height: 1;
        }
        .confirm-title span { color: var(--green); }
        .confirm-sub {
            font-size: 0.85rem;
            color: var(--muted);
            text-align: center;
            line-height: 1.6;
        }

        /* ── Divider ────────────────────────────────────────────── */
        .divider {
            width: 100%;
            height: 1px;
            background: var(--border);
        }

        /* ── Detalle pago ───────────────────────────────────────── */
        .detail-grid {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 480px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
        .detail-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem;
        }
        .detail-label {
            font-size: 0.68rem;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.3rem;
        }
        .detail-value {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text);
            word-break: break-all;
        }
        .detail-value.mono { font-family: monospace; font-size: 0.78rem; }
        .detail-value.green { color: var(--green); }

        /* ── Items de la orden ──────────────────────────────────── */
        .items-section {
            width: 100%;
        }
        .items-title {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.6rem;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
        }
        .item-row:last-child { border-bottom: none; }
        .item-name-wrap { display: flex; flex-direction: column; gap: 0.15rem; }
        .item-name { font-weight: 600; }
        .item-qty { font-size: 0.72rem; color: var(--muted); }
        .item-subtotal { font-weight: 700; color: var(--green); white-space: nowrap; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            margin-top: 0.25rem;
        }
        .total-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }
        .total-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.8rem;
            color: var(--green);
            letter-spacing: 0.04em;
        }

        /* ── QR Section ─────────────────────────────────────────── */
        .qr-section {
            width: 100%;
            background: rgba(34,197,94,0.04);
            border: 1px solid rgba(34,197,94,0.2);
            border-radius: 14px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }
        .qr-title {
            font-size: 0.72rem;
            color: var(--green);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .qr-subtitle {
            font-size: 0.72rem;
            color: var(--muted);
            text-align: center;
        }
        #qr-canvas {
            background: white;
            padding: 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #qr-canvas canvas,
        #qr-canvas img {
            display: block;
        }

        /* ── Botón volver ───────────────────────────────────────── */
        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            max-width: 320px;
            padding: 0.9rem 1.5rem;
            background: var(--green);
            color: #000;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-home:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }
        .btn-compras {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            max-width: 320px;
            padding: 0.75rem 1.5rem;
            background: transparent;
            color: var(--muted);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.78rem;
            letter-spacing: 0.05em;
            border: 1px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-compras:hover {
            border-color: var(--green);
            color: var(--green);
        }

        .actions-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }

        /* ── Nota final ─────────────────────────────────────────── */
        .thanks-note {
            font-size: 0.75rem;
            color: var(--muted);
            text-align: center;
            line-height: 1.6;
        }
        .thanks-note strong { color: var(--text); }
    </style>
</head>
<body>

{{-- Topbar --}}
<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">
        {{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}
    </a>
    <span class="order-badge">Pago exitoso ✓</span>
</header>

{{-- Card principal --}}
<div class="confirm-card">

    {{-- Ícono --}}
    <div class="success-ring">✅</div>

    {{-- Badges y título --}}
    <span class="order-badge">Orden #{{ $order->id }}</span>
    <h1 class="confirm-title">¡PAGO <span>EXITOSO</span>!</h1>
    <p class="confirm-sub">
        Tu pago fue procesado correctamente por Payphone.<br>
        Guarda el QR como comprobante de tu transacción.
    </p>

    <div class="divider"></div>

    {{-- Detalle del pago --}}
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Cliente</div>
            <div class="detail-value">{{ $order->user->name ?? 'Invitado' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Fecha</div>
            <div class="detail-value">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Total pagado</div>
            <div class="detail-value green">${{ number_format($order->total, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Estado</div>
            <div class="detail-value green">Aprobado ✓</div>
        </div>
        @if($order->transaction && $order->transaction->payphone_ref)
        <div class="detail-item" style="grid-column: 1 / -1;">
            <div class="detail-label">Referencia Payphone</div>
            <div class="detail-value mono">{{ $order->transaction->payphone_ref }}</div>
        </div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Items --}}
    <div class="items-section">
        <div class="items-title">Detalle del pedido</div>
        @foreach($order->items as $item)
        <div class="item-row">
            <div class="item-name-wrap">
                <span class="item-name">{{ $item->itemable->name ?? $item->item_display_name }}</span>
                <span class="item-qty">{{ $item->item_type_label }} × {{ $item->quantity }}</span>
            </div>
            <span class="item-subtotal">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
        </div>
        @endforeach
        <div class="total-row">
            <span class="total-label">Total pagado</span>
            <span class="total-value">${{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    {{-- QR de respaldo --}}
    <div class="qr-section">
        <div class="qr-title">📱 Código QR de respaldo</div>
        <div id="qr-canvas"></div>
        <div class="qr-subtitle">
            Escanea este código para ver el detalle de tu orden.<br>
            Guarda una captura de pantalla como comprobante.
        </div>
    </div>

    <div class="divider"></div>

    {{-- Acciones --}}
    <div class="actions-wrap">
        <a href="{{ route('home') }}" class="btn-home">
            ← Volver al inicio
        </a>
        @auth
        <a href="{{ route('customer.compras') }}" class="btn-compras">
            📋 Ver mis compras
        </a>
        @endauth
    </div>

    <p class="thanks-note">
        ¡Gracias por confiar en <strong>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</strong>!<br>
        Tu vehículo quedará en las mejores manos. 🚗✨
    </p>

</div>

@include('website.whatsapp-float')

<script>
    // Datos para el QR — todo lo relevante de la transacción
    const qrData = {
        orden:    '#{{ $order->id }}',
        cliente:  '{{ addslashes($order->user->name ?? "Invitado") }}',
        email:    '{{ addslashes($order->user->email ?? "") }}',
        total:    '${{ number_format($order->total, 2) }}',
        estado:   'Pagado',
        fecha:    '{{ $order->updated_at->format("d/m/Y H:i") }}',
        ref:      '{{ $order->transaction->payphone_ref ?? "" }}',
        items:    @json($order->items->map(fn($i) => ($i->itemable->name ?? $i->item_display_name) . ' x' . $i->quantity)),
        link:     '{{ route("orden.confirmacion", $order) }}',
        empresa:  '{{ addslashes($empresa->nombre ?? "Endara Carwash") }}',
    };

    // Convertir a texto legible para el QR
    const qrText = [
        qrData.empresa,
        '━━━━━━━━━━━━━━━━━━',
        'Orden: ' + qrData.orden,
        'Cliente: ' + qrData.cliente,
        qrData.email ? 'Email: ' + qrData.email : '',
        'Fecha: ' + qrData.fecha,
        'Total: ' + qrData.total,
        'Estado: ' + qrData.estado,
        qrData.ref ? 'Ref. Payphone: ' + qrData.ref : '',
        '━━━━━━━━━━━━━━━━━━',
        'Servicios/Productos:',
        ...qrData.items.map(i => '• ' + i),
        '━━━━━━━━━━━━━━━━━━',
        'Verificar en: ' + qrData.link,
    ].filter(Boolean).join('\n');

    // Generar QR
    document.addEventListener('DOMContentLoaded', () => {
        new QRCode(document.getElementById('qr-canvas'), {
            text:           qrText,
            width:          200,
            height:         200,
            colorDark:      '#000000',
            colorLight:     '#ffffff',
            correctLevel:   QRCode.CorrectLevel.M,
        });
    });
</script>

</body>
</html>