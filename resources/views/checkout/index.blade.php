<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Checkout â€” {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- PayPhone Box v2.0 --}}
    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v2.0/payphone-payment-box.css">
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v2.0/payphone-payment-box.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/scss/checkout.scss', 'resources/js/app.js'])
</head>
<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">
        {{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}
    </a>
    <div class="steps">
        <span class="step done">Carrito</span>
        <span class="step-sep">â€º</span>
        <span class="step active">Resumen</span>
        <span class="step-sep">â€º</span>
        <span class="step">Pago</span>
        <span class="step-sep">â€º</span>
        <span class="step">ConfirmaciÃ³n</span>
    </div>
</header>

<div class="container">
    <h1 class="page-title">RESUMEN DEL <span>PEDIDO</span></h1>
    <p class="page-sub">Revisa tu pedido antes de proceder al pago</p>

    <div class="checkout-grid">

        {{-- Columna izquierda: datos + Ã­tems --}}
        <div>
            {{-- Datos del cliente --}}
            <div class="card">
                <div class="card-header"><h3>Datos del cliente</h3></div>
                <div class="card-body">
                    <div class="cliente-row">
                        <div class="cliente-avatar">
                            <x-heroicon-o-user class="w-8 h-8 text-gray-500" />
                        </div>
                        <div>
                            @auth
                                <div class="cliente-name">{{ auth()->user()->name }}</div>
                                <div class="cliente-email">{{ auth()->user()->email }}</div>
                            @else
                                <div class="cliente-name">Cliente Invitado</div>
                                <div class="cliente-email">Compra sin iniciar sesiÃ³n</div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ãtems --}}
            <div class="card">
                <div class="card-header">
                    <h3>Ãtems del pedido ({{ count($carrito) }})</h3>
                </div>
                <div class="card-body">
                    @foreach($carrito as $item)
                    <div class="item-row">
                        <div class="item-emoji">
                            @if($item['type'] === 'product')
                                <x-heroicon-o-cube class="w-6 h-6 text-gray-500" />
                            @elseif($item['type'] === 'service')
                                <x-heroicon-o-wrench class="w-6 h-6 text-gray-500" />
                            @else
                                <x-heroicon-o-archive-box class="w-6 h-6 text-gray-500" />
                            @endif
                        </div>
                        <div class="item-info">
                            <div class="item-name">{{ $item['name'] }}</div>
                            <div class="item-qty">
                                {{ $item['type_label'] ?? ($item['type'] === 'product' ? 'Producto' : ($item['type'] === 'service' ? 'Servicio' : 'CatÃ¡logo')) }}
                                Ã— {{ $item['quantity'] }}
                            </div>
                        </div>
                        <div class="item-price">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card" id="payphone-section" style="display:none;">
                <div class="card-header">
                    <h3>Completa tu pago</h3>
                </div>
                <div class="card-body">
                    <div id="pp-button"></div>
                    <div id="pay-status" style="display:none; margin-top:10px; font-size:0.8rem; color:#888; text-align:center;"></div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: resumen + cajita --}}
        <div class="resumen-card">
            <div class="resumen-title">Total a Pagar</div>

            @foreach($carrito as $item)
            <div class="resumen-row">
                <span>{{ Str::limit($item['name'], 22) }} Ã—{{ $item['quantity'] }}</span>
                <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
            </div>
            @endforeach

            <hr class="resumen-divider">

            <div class="resumen-total">
                <span class="resumen-total-label">Total</span>
                <span class="resumen-total-value">${{ number_format($total, 2) }}</span>
            </div>

            {{-- Badge seguridad --}}
            <div class="payphone-badge">
                <x-heroicon-o-credit-card class="w-6 h-6 text-gray-500" />
                <div>
                    <strong>Pago seguro con Payphone</strong><br>
                    <span>Tarjeta de crÃ©dito o dÃ©bito</span>
                </div>
            </div>

            {{-- BotÃ³n que abre la cajita --}}
            <button type="button" class="btn-pay" id="pay-btn-box">
                <x-heroicon-o-credit-card class="w-5 h-5 inline mr-2" />
                Pagar ${{ number_format($total, 2) }} con PayPhone
            </button>

            {{-- AquÃ­ se renderiza la cajita de PayPhone --}}
            

            <a href="{{ route('carrito.index') }}" class="btn-back flex items-center justify-center gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4 inline mr-1"/>
                Volver al carrito
            </a>

            <p class="secure-note">
                <x-heroicon-o-lock-closed class="w-4 h-4 inline mr-1" />
                Pago 100% seguro procesado por Payphone
            </p>
        </div>

    </div>
</div>

@include('website.whatsapp-float')

<script>
    (() => {
        const payBtn = document.getElementById('pay-btn-box');
        const statusEl = document.getElementById('pay-status');
        const paymentSection = document.getElementById('payphone-section');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const endpoint = @json(route('orden.cajita'));

        function setStatus(msg) {
            if (!statusEl) return;
            statusEl.style.display = msg ? 'block' : 'none';
            statusEl.textContent = msg;
        }

        if (!payBtn || !csrf) return;

        payBtn.addEventListener('click', async () => {
            payBtn.disabled = true;
            payBtn.textContent = 'Preparando pago...';
            setStatus('Conectando con Payphone...');

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await res.json();

                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'No se pudo preparar el pago.');
                }

                if (typeof PPaymentButtonBox !== 'function') {
                    throw new Error('El modulo de pago no cargo. Recarga la pagina e intenta de nuevo.');
                }

                payBtn.style.display = 'none';
                if (paymentSection) {
                    paymentSection.style.display = 'block';
                    paymentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                setStatus('Ingresa los datos de tu tarjeta para completar el pago.');

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

            } catch (err) {
                console.error('PayPhone error:', err);
                setStatus('');
                payBtn.disabled = false;
                payBtn.style.display = 'inline-flex';
                payBtn.textContent = 'Pagar ${{ number_format($total, 2) }} con PayPhone';
                if (paymentSection) {
                    paymentSection.style.display = 'none';
                }
                alert(err.message || 'Ocurrio un error. Intenta de nuevo.');
            }
        });
    })();
</script>
</body>
</html>
