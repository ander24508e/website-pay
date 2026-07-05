<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago exitoso - {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/checkout-confirmacion.scss', 'resources/js/app.js'])
</head>

<body style="--ok: {{ $primario }}; --ok-dark: color-mix(in srgb, {{ $primario }} 82%, black); --accent: {{ $secundario }}; --muted: {{ $terciario }};">
    <header class="topbar">
        <a href="{{ route('home') }}">{{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}</a>
        <span class="verified-badge">Pago verificado</span>
    </header>

    <main class="receipt-layout">
        <section class="receipt" aria-label="Comprobante de pago">
            <div class="receipt-hero">
                <div class="qr-card">
                    <img src="{{ $qrCodeDataUri }}" alt="QR de verificaci&oacute;n de {{ $orderCode }}" class="receipt-qr-image">
                </div>
                <h1 class="receipt-title">Pago Exitoso</h1>
                <p class="receipt-subtitle">Tu compra ha sido realizada correctamente.</p>
            </div>

            <div class="receipt-body">
                <div class="detail-list">
                    <div class="detail-row">
                        <span>N&uacute;mero de orden:</span>
                        <strong>{{ $orderCode }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Cliente:</span>
                        <strong>{{ $order->user->name ?? 'Invitado' }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Fecha y hora:</span>
                        <strong>{{ $order->updated_at->format('d/m/Y - H:i') }}</strong>
                    </div>
                    <div class="detail-row">
                        <span>Canal:</span>
                        <strong>PayPhone</strong>
                    </div>
                    <div class="detail-row receipt-items">
                        <span>Detalle de Compra:</span>
                        <strong class="receipt-items-list">
                            @foreach ($itemsSummary as $itemSummary)
                                <span>{{ $itemSummary }}</span>
                            @endforeach
                        </strong>
                    </div>
                    <div class="detail-row total">
                        <span>Total pagado:</span>
                        <strong>${{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>

                <p class="receipt-note">
                    Escanea el QR para verificar este comprobante directamente en el sistema.
                </p>
            </div>
        </section>

        <aside class="desktop-panel">
            <section class="panel-card action-card">
                <h3>Acciones</h3>
                <p class="action-copy">Abre WhatsApp con los datos del comprobante. Adjunta la captura manualmente si necesitas enviar imagen.</p>
                <div class="actions">
                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                            class="btn btn-whatsapp">
                            Enviar por WhatsApp
                        </a>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-main">Regresar</a>
                    @auth
                        <a href="{{ route('customer.compras') }}" class="btn btn-ghost btn-full">Ver mis compras</a>
                    @endauth
                </div>
            </section>
        </aside>
    </main>

</body>

</html>
