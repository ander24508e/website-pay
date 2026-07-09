<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante {{ $orderCode }}</title>
    @if (!empty($inlineStyles))
        <style>
            {!! $inlineStyles !!}
        </style>
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link
            href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/scss/checkout-comprobante.scss'])
    @endif
</head>

<body style="--brand: {{ $primario }}; --brand-dark: color-mix(in srgb, {{ $primario }} 82%, black);">
    <main class="voucher">
        <header class="voucher-hero">
            <div class="voucher-hero-pattern"></div>
            <img src="{{ $receiptLogoUrl ?? $empresa?->logo_url }}" alt="{{ $empresa->nombre ?? 'Logo del negocio' }}"
                class="voucher-logo">
        </header>

        <section class="voucher-body">
            <div class="voucher-qr">
                <img src="{{ $qrCodeDataUri }}" alt="QR de verificaci&oacute;n de {{ $orderCode }}">
            </div>

            <h1>&iexcl;Pago <span>Exitoso!</span></h1>
            <p class="voucher-subtitle">Tu compra ha sido realizada correctamente.</p>

            <section class="voucher-details">
                <div class="voucher-icons" aria-hidden="true">
                    <span><x-heroicon-o-receipt-percent class="voucher-icon" /></span>
                    <span><x-heroicon-o-user class="voucher-icon" /></span>
                    <span><x-heroicon-o-calendar-days class="voucher-icon" /></span>
                    <span><x-heroicon-o-shopping-bag class="voucher-icon" /></span>
                    <span><x-heroicon-o-currency-dollar class="voucher-icon" /></span>
                </div>

                <div class="voucher-rows">
                    <div class="voucher-row">
                        <span>N&uacute;mero de orden:</span>
                        <strong>{{ $orderCode }}</strong>
                    </div>
                    <div class="voucher-row">
                        <span>Cliente:</span>
                        <strong>{{ $order->user->name ?? 'Invitado' }}</strong>
                    </div>
                    <div class="voucher-row">
                        <span>Fecha y hora:</span>
                        <strong>{{ ($order->transaction?->updated_at ?? $order->updated_at)->timezone(config('app.timezone'))->format('d/m/Y - H:i') }}</strong>
                    </div>
                    <div class="voucher-row">
                        <span>Detalle de compra:</span>
                        <strong>
                            @foreach ($itemsSummary as $itemSummary)
                                <small>{{ $itemSummary }}</small>
                            @endforeach
                        </strong>
                    </div>
                    <div class="voucher-row voucher-total">
                        <span>Total pagado:</span>
                        <strong>${{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
            </section>

            <p class="voucher-note">Escanea el QR para verificar este comprobante directamente en el sistema.</p>
        </section>

        <footer class="voucher-footer">
            <div>
                <span>Gracias por confiar en</span>
                <strong>{{ $empresa->nombre ?? 'Mi negocio' }}</strong>
            </div>
            <div class="voucher-secure">
                <x-heroicon-o-shield-check class="voucher-secure-icon" />
                <strong>Transacci&oacute;n segura</strong>
            </div>
        </footer>
    </main>
</body>

</html>
