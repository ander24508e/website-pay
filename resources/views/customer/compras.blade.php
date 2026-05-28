<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mis Compras</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/scss/profile/customer-compras.scss', 'resources/js/app.js'])
    <style>
        /* Estilos responsive adicionales (sin alterar colores ni funcionalidad) */
        @media (max-width: 768px) {
            .topbar {
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.75rem 1rem;
                height: auto;
            }
            .topbar-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.75rem;
            }
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .order-header > div:first-child {
                width: 100%;
            }
            .order-header div:last-child {
                justify-content: space-between;
                width: 100%;
            }
            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .item-price {
                align-self: flex-end;
            }
            .container {
                padding: 1rem;
            }
        }
        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }
            .badge, .order-total {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .order-id {
                font-size: 1rem;
            }
            .btn-primary {
                width: 100%;
                text-align: center;
            }
        }
        /* Corrección de caracteres */
        .page-sub {
            word-break: break-word;
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('customer.compras') }}" class="active">Mis Compras</a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button class="btn-logout">Salir</button>
        </form>
    </nav>
</header>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">MIS <span>COMPRAS</span></h1>
        <p class="page-sub">Historial de órdenes de {{ auth()->user()->name }}</p>
    </div>

    @if($orders->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🛒</div>
            <p class="empty-text">Aún no tienes compras registradas.</p>
            <a href="{{ route('home') }}#catalogo" class="btn-primary">Ver Catálogo</a>
        </div>
    @else
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-id">Orden #{{ $order->id }}</div>
                    <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:1rem; flex-wrap:wrap;">
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

</body>
</html>