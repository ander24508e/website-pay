<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Orden #{{ $order->id }} — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])
</head>
<body class="checkout-show">

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
@include('website.whatsapp-float')
</body>
</html>
