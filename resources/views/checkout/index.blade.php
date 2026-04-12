<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Checkout — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])
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