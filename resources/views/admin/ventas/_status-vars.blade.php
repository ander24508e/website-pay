@php
    $statusClass = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'reserved' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-green-100 text-green-700',
        'failed' => 'bg-red-100 text-red-700',
        'cancelled' => 'bg-gray-100 text-gray-600',
    ][$status] ?? 'bg-gray-100 text-gray-600';

    $statusLabel = [
        'pending' => 'Pendiente',
        'reserved' => 'Reservada',
        'paid' => 'Pagada',
        'failed' => 'Fallida',
        'cancelled' => 'Cancelada',
    ][$status] ?? ucfirst((string) $status);
@endphp
