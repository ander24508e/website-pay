<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    @php($primario = $empresa->color_primario_hex ?? '#22c55e')
    @php($secundario = $empresa->color_secundario_hex ?? '#0ea5e9')
    @php($terciario = $empresa->color_terciario_hex ?? '#94a3b8')
    <title>Pago exitoso - {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --ok: {{ $primario }};
            --ok-dark: color-mix(in srgb, {{ $primario }} 82%, black);
            --accent: {{ $secundario }};
            --muted: {{ $terciario }};
            --bg: #0b0f14;
            --card: #131a22;
            --line: rgba(255,255,255,.12);
            --text: #e5e7eb;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:var(--text);padding:1rem}
        .topbar{max-width:880px;margin:0 auto 1rem;display:flex;justify-content:space-between;align-items:center;background:rgba(0,0,0,.35);border:1px solid var(--line);padding:.8rem 1rem;border-radius:12px}
        .topbar a{font-family:'Bebas Neue',sans-serif;color:#fff;text-decoration:none;letter-spacing:.08em;font-size:1.35rem}
        .badge{font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;border:1px solid color-mix(in srgb, var(--ok) 65%, transparent);color:var(--ok);padding:.3rem .65rem;border-radius:999px}
        .wrap{max-width:880px;margin:0 auto;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:1.1rem}
        .ok{width:70px;height:70px;border-radius:999px;border:2px solid var(--ok);display:grid;place-items:center;margin:0 auto .6rem;font-size:1.8rem;background:color-mix(in srgb, var(--ok) 16%, transparent)}
        h1{font-family:'Bebas Neue',sans-serif;text-align:center;font-size:2.1rem;letter-spacing:.06em}
        h1 span{color:var(--ok)}
        .sub{text-align:center;color:var(--muted);font-size:.85rem;margin:.35rem 0 1rem}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:.65rem;margin-bottom:1rem}
        .box{border:1px solid var(--line);border-radius:10px;padding:.7rem .8rem;background:rgba(255,255,255,.02)}
        .k{font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.2rem}
        .v{font-size:.86rem;font-weight:600;word-break:break-word}
        .items{border:1px solid var(--line);border-radius:10px;padding:.6rem .8rem;margin-bottom:1rem}
        .row{display:flex;justify-content:space-between;gap:.7rem;padding:.45rem 0;border-bottom:1px solid rgba(255,255,255,.07)}
        .row:last-child{border-bottom:none}
        .tot{font-family:'Bebas Neue',sans-serif;font-size:1.6rem;color:var(--ok)}
        .qr{border:1px solid color-mix(in srgb, var(--accent) 50%, transparent);border-radius:10px;padding:.8rem;display:grid;place-items:center;gap:.5rem;margin-bottom:1rem}
        #qr-canvas{background:#fff;padding:10px;border-radius:8px}
        .actions{display:flex;flex-direction:column;gap:.6rem;align-items:center}
        .btn{display:inline-flex;justify-content:center;align-items:center;width:100%;max-width:340px;padding:.78rem 1rem;border-radius:10px;text-decoration:none;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700}
        .btn-main{background:var(--ok);color:#051014}
        .btn-main:hover{background:var(--ok-dark)}
        .btn-ghost{border:1px solid var(--line);color:var(--muted)}
        @media (max-width: 760px){
            .topbar{flex-direction:column;gap:.5rem;align-items:flex-start}
            .grid{grid-template-columns:1fr}
        }
    </style>
    @vite(['resources/css/app.css','resources/scss/checkout.scss','resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <a href="{{ route('home') }}">{{ strtoupper($empresa->nombre_corto ?? 'CARWASH') }}</a>
    <span class="badge">Pago exitoso</span>
</header>

<main class="wrap">
    <div class="ok">✓</div>
    <h1>PAGO <span>EXITOSO</span></h1>
    <p class="sub">Tu pago fue procesado correctamente. Guarda este comprobante.</p>

    <section class="grid">
        <article class="box"><div class="k">Cliente</div><div class="v">{{ $order->user->name ?? 'Invitado' }}</div></article>
        <article class="box"><div class="k">Fecha</div><div class="v">{{ $order->updated_at->format('d/m/Y H:i') }}</div></article>
        <article class="box"><div class="k">Total pagado</div><div class="v">${{ number_format($order->total,2) }}</div></article>
        <article class="box"><div class="k">Estado</div><div class="v">Aprobado</div></article>
    </section>

    <section class="items">
        @foreach($order->items as $item)
        <div class="row">
            <span>{{ $item->itemable->name ?? $item->item_display_name }} x {{ $item->quantity }}</span>
            <strong>${{ number_format($item->unit_price * $item->quantity, 2) }}</strong>
        </div>
        @endforeach
        <div class="row"><span>Total</span><span class="tot">${{ number_format($order->total,2) }}</span></div>
    </section>

    <section class="qr">
        <div id="qr-canvas"></div>
        <small style="color:var(--muted)">QR de verificación de la orden</small>
    </section>

    <div class="actions">
        <a href="{{ route('home') }}" class="btn btn-main">Volver al inicio</a>
        @auth
        <a href="{{ route('customer.compras') }}" class="btn btn-ghost">Ver mis compras</a>
        @endauth
    </div>
</main>

<script>
    const qrPayload = {
        company: @json($empresa->nombre ?? 'Endara Carwash'),
        orderId: {{ $order->id }},
        paidAt: @json($order->updated_at->toIso8601String()),
        amount: {{ (float) $order->total }},
        status: 'approved',
        payphoneRef: @json(optional($order->transaction)->payphone_ref),
        clientTransactionId: @json(optional($order->transaction)->client_transaction_id),
        verificationUrl: @json(route('orden.confirmacion', $order)),
    };
    document.addEventListener('DOMContentLoaded', () => {
        new QRCode(document.getElementById('qr-canvas'), {
            text: JSON.stringify(qrPayload), width: 190, height: 190,
            colorDark: '#000000', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M,
        });
    });
</script>
</body>
</html>
