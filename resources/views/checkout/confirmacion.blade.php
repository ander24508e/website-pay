<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Pago Exitoso — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])
</head>
<body class="checkout-confirmacion">

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
        @auth
            <a href="{{ route('customer.compras') }}" class="btn-primary">
                📋 Ver mis compras
            </a>
        @else
            <a href="{{ route('carrito.index') }}" class="btn-primary">
                🛒 Volver al carrito
            </a>
        @endauth
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
