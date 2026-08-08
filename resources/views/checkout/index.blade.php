<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $empresa = App\Models\Empresa::first();
        $primario = $empresa->color_primario_hex ?? '#D82128';
        $secundario = $empresa->color_secundario_hex ?? '#F0B429';
        $terciario = $empresa->color_terciario_hex ?? '#666666';
    @endphp

    <title>Checkout - {{ $empresa->nombre ?? 'Endara Carwash' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v2.0/payphone-payment-box.css">
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v2.0/payphone-payment-box.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --red: {{ $primario }};
            --red-dark: color-mix(in srgb, {{ $primario }} 82%, black);
            --gold: {{ $secundario }};
            --muted: {{ $terciario }};
        }
    </style>

    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])
</head>

<body class="checkout-page">

    <header class="checkout-topbar">
        <a href="{{ route('home') }}" class="checkout-brand">
            {{ strtoupper($empresa->nombre_corto ?? 'LAVADORA Y LUBRICADORA ENDARA') }}
        </a>

        <nav class="checkout-steps">
            <span class="step done">Carrito</span>
            <span class="step-sep">›</span>
            <span class="step active">Resumen</span>
            <span class="step-sep">›</span>
            <span class="step">Pago</span>
            <span class="step-sep">›</span>
            <span class="step">Confirmación</span>
        </nav>
    </header>

    <main class="checkout-shell">

        <section class="checkout-summary-panel">

            <div class="checkout-heading">
                <h1>Resumen del <span>pedido</span></h1>
                <p>Revisa tu pedido antes de proceder al pago.</p>
            </div>

            <div class="checkout-card checkout-summary-card">
                <div class="checkout-card-header">
                    <h3>Resumen de compra</h3>
                    <span>{{ count($carrito) }} {{ count($carrito) === 1 ? 'Item' : 'Items' }}</span>
                </div>

                <div class="checkout-card-body">
                    <div class="summary-client">
                        <div class="client-row">
                            <div class="client-icon">
                                <x-heroicon-o-user class="w-7 h-7" />
                            </div>

                            <div class="min-w-0">
                                @auth
                                    <div class="client-name">{{ auth()->user()->name }}</div>
                                    <div class="client-email">{{ auth()->user()->email }}</div>
                                @else
                                    <div class="client-name">Cliente invitado</div>
                                    <div class="client-email">Compra sin iniciar sesi&oacute;n</div>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <div class="summary-items">
                        @foreach ($carrito as $item)
                            <div class="checkout-item">
                                <div class="checkout-item-icon">
                                    @if ($item['type'] === 'product')
                                        <x-heroicon-o-cube class="w-5 h-5" />
                                    @elseif($item['type'] === 'service')
                                        <x-heroicon-o-wrench class="w-5 h-5" />
                                    @else
                                        <x-heroicon-o-archive-box class="w-5 h-5" />
                                    @endif
                                </div>

                                <div class="checkout-item-info">
                                    <div class="checkout-item-name">{{ $item['name'] }}</div>
                                    <div class="checkout-item-meta">
                                        {{ $item['type_label'] ?? ($item['type'] === 'product' ? 'Producto' : ($item['type'] === 'service' ? 'Servicio' : 'Cat?logo')) }}
                                        &times; {{ $item['quantity'] }}
                                    </div>
                                </div>

                                <div class="checkout-item-price">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong>${{ number_format($total, 2) }}</strong>
                    </div>
                </div>

                <div class="checkout-actions">
                    <a href="{{ route('home') }}#catalogo" class="btn-catalogo">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        Comprar m&aacute;s
                    </a>
                </div>
            </div>
        </section>

        <section class="payment-panel">
            <div class="payment-card">

                <div class="payment-header">
                    <div>
                        <h2>Completar Pago Seguro</h2>
                        <p>Selecciona tu método de pago y finaliza tu compra.</p>
                    </div>

                    <div class="secure-mini">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        Seguro
                    </div>
                </div>

                <div class="payment-body">

                    <div class="payment-empty" id="payment-empty">
                        <div class="payment-methods-preview">
                            <span>VISA</span>
                            <span>Mastercard</span>
                            <span>Amex</span>
                            <span>PayPhone</span>
                        </div>

                        <div class="payment-placeholder">
                            <x-heroicon-o-credit-card class="w-10 h-10" />
                            <h3>Pago con PayPhone</h3>
                            <p>Presiona el botón para cargar la cajita de pago segura.</p>
                        </div>
                    </div>

                    <div id="payphone-section" class="payphone-box-wrap">
                        <div id="pp-button"></div>
                        <div id="pay-status"></div>
                    </div>

                </div>

                <div class="payment-footer">
                    <p class="secure-note">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        Pago 100% seguro procesado por PayPhone
                    </p>
                </div>

            </div>
        </section>

    </main>

    @include('website.whatsapp-float.index')

    <script>
        (() => {

            const statusEl = document.getElementById('pay-status');
            const paymentSection = document.getElementById('payphone-section');
            const paymentEmpty = document.getElementById('payment-empty');

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const endpoint = @json(route('orden.cajita'));

            function setStatus(message) {
                if (!statusEl) return;

                statusEl.style.display = message ? 'block' : 'none';
                statusEl.textContent = message || '';
            }

            async function loadPaymentBox() {

                try {

                    document.body.classList.add('payment-open');

                    setStatus('Conectando con PayPhone...');

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (!response.ok || !data.ok) {
                        throw new Error(data.message || 'No se pudo iniciar el pago.');
                    }

                    if (typeof PPaymentButtonBox !== 'function') {
                        throw new Error('PayPhone no cargó correctamente.');
                    }

                    if (paymentEmpty) {
                        paymentEmpty.style.display = 'none';
                    }

                    if (paymentSection) {
                        paymentSection.style.display = 'block';
                    }

                    setStatus('Ingresa los datos de tu tarjeta.');

                    new PPaymentButtonBox({
                        token: data.token,
                        clientTransactionId: data.clientTransactionId,
                        amount: data.amount,
                        amountWithoutTax: data.amountWithoutTax,
                        amountWithTax: data.amountWithTax,
                        tax: data.tax,
                        service: 0,
                        tip: 0,
                        currency: data.currency,
                        storeId: data.storeId,
                        reference: data.reference,
                        lang: 'es',
                        defaultMethod: 'card',
                        timeZone: data.timeZone,
                    }).render('pp-button');

                } catch (error) {

                    console.error('PayPhone Error:', error);

                    document.body.classList.remove('payment-open');

                    setStatus('');

                    if (paymentEmpty) {
                        paymentEmpty.style.display = 'flex';
                    }

                    if (paymentSection) {
                        paymentSection.style.display = 'none';
                    }

                    alert(error.message || 'Error cargando PayPhone.');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                loadPaymentBox();
            });

        })();
    </script>

</body>

</html>
