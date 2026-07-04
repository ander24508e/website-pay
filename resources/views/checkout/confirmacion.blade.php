<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $empresa = App\Models\Empresa::first();
        $primario = $empresa?->color_primario_hex ?? '#D82128';
        $secundario = $empresa?->color_secundario_hex ?? '#F0B429';
        $terciario = $empresa?->color_terciario_hex ?? '#94a3b8';
        $transaction = $order->transaction;
        $verificationUrl = route('orden.confirmacion', $order);
        $orderCode = 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $storedWhatsappUrl = trim((string) ($empresa?->getRawOriginal('whatsapp_url') ?? ''));
        $storedPhone = trim((string) ($empresa?->telefono ?? ''));
        $whatsappMessage = implode("\n", [
            'Hola, adjunto la captura de mi comprobante de pago.',
            'Orden: ' . $orderCode,
            'Total: $' . number_format($order->total, 2),
            'Verificacion QR: ' . $verificationUrl,
        ]);

        $extractWhatsappPhone = function (?string $url): ?string {
            $url = trim((string) $url);

            if ($url === '') {
                return null;
            }

            if (preg_match('~wa\.me/(\d+)~i', $url, $matches)) {
                return $matches[1];
            }

            $query = parse_url($url, PHP_URL_QUERY);
            parse_str((string) $query, $params);

            if (!empty($params['phone'])) {
                return preg_replace('/\D+/', '', (string) $params['phone']);
            }

            return null;
        };

        $normalizeWhatsappPhone = function (?string $phone): ?string {
            $digits = preg_replace('/\D+/', '', (string) $phone);

            if ($digits === '') {
                return null;
            }

            if (str_starts_with($digits, '0') && strlen($digits) === 10) {
                return '593' . substr($digits, 1);
            }

            if (str_starts_with($digits, '9') && strlen($digits) === 9) {
                return '593' . $digits;
            }

            return $digits;
        };

        $whatsappPhone = $extractWhatsappPhone($storedWhatsappUrl) ?: $normalizeWhatsappPhone($storedPhone);
        $whatsappUrl = $whatsappPhone
            ? 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode($whatsappMessage)
            : null;
    @endphp
    <title>Pago exitoso - {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
                    <div id="qr-canvas"></div>
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
                            @foreach ($order->items as $item)
                                <span>{{ $item->itemable->name ?? $item->item_display_name }} &times; {{ $item->quantity }}</span>
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
                <p class="action-copy">Env&iacute;a la captura del comprobante por WhatsApp o regresa al inicio.</p>
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

    <script>
        const verificationUrl = @json($verificationUrl);

        document.addEventListener('DOMContentLoaded', () => {
            const options = {
                text: verificationUrl,
                width: 116,
                height: 116,
                colorDark: '#101010',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            };

            new QRCode(document.getElementById('qr-canvas'), options);

        });
    </script>
</body>

</html>
