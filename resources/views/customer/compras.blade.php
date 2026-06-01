<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    @php
        $empresa = App\Models\Empresa::first() ?? new App\Models\Empresa();
        $primary = $empresa->color_primario_hex;
        $secondary = $empresa->color_secundario_hex;
        $tertiary = $empresa->color_terciario_hex;
        $darkenHex = function (string $hex, int $steps = 26): string {
            $hex = ltrim($hex, '#');
            $r = max(0, hexdec(substr($hex, 0, 2)) - $steps);
            $g = max(0, hexdec(substr($hex, 2, 2)) - $steps);
            $b = max(0, hexdec(substr($hex, 4, 2)) - $steps);
            return sprintf('#%02X%02X%02X', $r, $g, $b);
        };
        $brandName = strtoupper($empresa->nombre_corto ?? $empresa->nombre ?? 'ENDARA CARWASH');
        $brandParts = preg_split('/\s+/', trim($brandName));
        $brandMain = implode(' ', array_slice($brandParts, 0, 1));
        $brandAccent = implode(' ', array_slice($brandParts, 1));
    @endphp
    <title>Mis Compras — {{ $empresa->nombre ?? 'Endara Carwash' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/profile/customer-compras.scss', 'resources/js/app.js'])
</head>
<body
    data-brand-primary="{{ $primary }}"
    data-brand-primary-dark="{{ $darkenHex($primary) }}"
    data-brand-secondary="{{ $secondary }}"
    data-brand-tertiary="{{ $tertiary }}">

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">
        {{ $brandMain }}
        @if ($brandAccent)
            <span>{{ $brandAccent }}</span>
        @endif
    </a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('customer.compras') }}" class="active">Mis Compras</a>
        <a href="{{ route('profile.edit') }}">Perfil</a>
        <form method="POST" action="{{ route('logout') }}" class="inline-form">
            @csrf
            <button class="btn-logout">Salir</button>
        </form>
    </nav>
</header>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">MIS <span>COMPRAS</span></h1>
        <p class="page-sub">Historial de ordenes de {{ auth()->user()->name }}</p>
    </div>

    @if($orders->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <p class="empty-text">Aun no tienes compras registradas.</p>
            <a href="{{ route('home') }}#catalogo" class="btn-primary">Ver Catalogo</a>
        </div>
    @else
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-id">Orden #{{ $order->id }}</div>
                    <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="order-header-meta">
                    @php
                        $badges = [
                            'pending' => 'badge-pending',
                            'paid' => 'badge-paid',
                            'cancelled' => 'badge-cancelled',
                            'failed' => 'badge-failed',
                            'reserved' => 'badge-pending',
                        ];
                        $labels = [
                            'pending' => 'Pendiente',
                            'paid' => 'Pagado',
                            'cancelled' => 'Cancelado',
                            'failed' => 'Fallido',
                            'reserved' => 'Reservado',
                        ];
                    @endphp
                    <span class="badge {{ $badges[$order->status] ?? 'badge-pending' }}">
                        {{ $labels[$order->status] ?? $order->status }}
                    </span>
                    <div class="order-total">${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
            <div class="order-items">
                @foreach($order->items as $item)
                <div class="order-item">
                    <div>
                        <div class="item-name">{{ $item->item_display_name }}</div>
                        <div class="item-type">{{ $item->item_type_label }} × {{ $item->quantity }}</div>
                    </div>
                    <div class="item-price">${{ number_format($item->unit_price * $item->quantity, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif
</div>

<script>
function initComprasBrandVars() {
    const root = document.body;
    const primary = root.dataset.brandPrimary;
    const primaryDark = root.dataset.brandPrimaryDark;
    const secondary = root.dataset.brandSecondary;
    const tertiary = root.dataset.brandTertiary;
    if (!primary || !primaryDark || !secondary || !tertiary) return;

    root.style.setProperty('--brand-primary', primary);
    root.style.setProperty('--brand-primary-dark', primaryDark);
    root.style.setProperty('--brand-secondary', secondary);
    root.style.setProperty('--brand-tertiary', tertiary);
}

document.addEventListener('DOMContentLoaded', initComprasBrandVars);
</script>
</body>
</html>
