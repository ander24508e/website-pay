<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compras — Endara Carwash</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --red:#d82128; --red-dark:#b41b21; --gold:#f0b429; --dark:#1e1e1e; --dark-2:#141414; --dark-3:#0a0a0a; --muted:#666666; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Montserrat',sans-serif; background:var(--dark-3); color:white; min-height:100vh; }
        h1,h2,h3 { font-family:'Bebas Neue',sans-serif; letter-spacing:0.05em; }

        .topbar { background:rgba(10,10,10,0.95); border-bottom:1px solid rgba(216,33,40,0.2); padding:0 2rem; height:70px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .topbar-brand { font-family:'Bebas Neue',sans-serif; font-size:1.3rem; color:white; text-decoration:none; }
        .topbar-brand span { color:var(--red); }
        .topbar-nav { display:flex; align-items:center; gap:1.5rem; }
        .topbar-nav a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.78rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; transition:color 0.2s; }
        .topbar-nav a:hover, .topbar-nav a.active { color:var(--gold); }
        .btn-logout { background:transparent; color:rgba(255,255,255,0.4); border:1px solid rgba(255,255,255,0.1); padding:0.4rem 1rem; border-radius:4px; font-size:0.72rem; font-weight:600; letter-spacing:0.1em; cursor:pointer; transition:all 0.2s; }
        .btn-logout:hover { border-color:var(--red); color:var(--red); }

        .container { max-width:1000px; margin:0 auto; padding:3rem 2rem; }
        .page-header { margin-bottom:2.5rem; }
        .page-title { font-size:2.5rem; color:white; }
        .page-title span { color:var(--red); }
        .page-sub { color:var(--muted); font-size:0.85rem; margin-top:0.3rem; }

        .empty-state { text-align:center; padding:5rem 2rem; color:var(--muted); }
        .empty-icon { font-size:3rem; margin-bottom:1rem; }
        .empty-text { font-size:0.9rem; margin-bottom:1.5rem; }
        .btn-primary { background:var(--red); color:white; padding:0.75rem 2rem; border-radius:4px; font-weight:700; font-size:0.82rem; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; transition:all 0.2s; display:inline-block; }
        .btn-primary:hover { background:var(--red-dark); }

        .order-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; margin-bottom:1.5rem; overflow:hidden; transition:border-color 0.2s; }
        .order-card:hover { border-color:rgba(216,33,40,0.3); }
        .order-header { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.04); }
        .order-id { font-family:'Bebas Neue',sans-serif; font-size:1.1rem; color:var(--gold); }
        .order-date { font-size:0.75rem; color:var(--muted); }
        .order-total { font-family:'Bebas Neue',sans-serif; font-size:1.4rem; color:var(--red); }
        .badge { padding:0.3rem 0.8rem; border-radius:999px; font-size:0.68rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; }
        .badge-pending   { background:rgba(240,180,41,0.15); color:var(--gold); border:1px solid rgba(240,180,41,0.3); }
        .badge-paid      { background:rgba(40,167,69,0.15);  color:#28a745;     border:1px solid rgba(40,167,69,0.3); }
        .badge-cancelled { background:rgba(102,102,102,0.15);color:var(--muted); border:1px solid rgba(102,102,102,0.3); }
        .badge-failed    { background:rgba(216,33,40,0.15);  color:var(--red);  border:1px solid rgba(216,33,40,0.3); }

        .order-items { padding:1rem 1.5rem; }
        .order-item { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px solid rgba(255,255,255,0.03); }
        .order-item:last-child { border-bottom:none; }
        .item-name { font-size:0.85rem; color:rgba(255,255,255,0.8); }
        .item-type { font-size:0.65rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.1em; margin-top:0.15rem; }
        .item-price { font-size:0.85rem; font-weight:600; color:white; }
    </style>
</head>
<body>

{{-- Topbar --}}
<header class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">ENDARA <span>CARWASH</span></a>
    <nav class="topbar-nav">
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('cliente.compras') }}" class="active">Mis Compras</a>
        <a href="{{ route('cliente.perfil') }}">Mi Perfil</a>
        <form method="POST" action="{{ route('logout') }}">
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
            <a href="{{ route('home') }}#servicios" class="btn-primary">Ver Servicios</a>
        </div>
    @else
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-id">Orden #{{ $order->id }}</div>
                    <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:1rem;">
                    @php
                        $badges = ['pending'=>'badge-pending','paid'=>'badge-paid','cancelled'=>'badge-cancelled','failed'=>'badge-failed'];
                        $labels = ['pending'=>'Pendiente','paid'=>'Pagado','cancelled'=>'Cancelado','failed'=>'Fallido'];
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
                        <div class="item-name">{{ $item->itemable->name ?? 'Ítem eliminado' }}</div>
                        <div class="item-type">{{ $item->itemable_type === 'App\\Models\\Product' ? 'Producto' : 'Servicio' }} × {{ $item->quantity }}</div>
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