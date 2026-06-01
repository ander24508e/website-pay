@extends('layouts.admin')

@section('title', 'Editar Venta')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Editar Venta #{{ $venta->id }}</h2>
        <a href="{{ route('admin.ventas.show', $venta) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.ventas.update', $venta) }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Invitado</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected(old('user_id', $venta->user_id) == $cliente->id)>{{ $cliente->name }} ({{ $cliente->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                <input type="number" step="0.01" min="0" name="total" value="{{ old('total', $venta->total) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['pending','paid','reserved','failed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $venta->status) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="order_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="purchase" @selected(old('order_type', $venta->order_type) === 'purchase')>purchase</option>
                    <option value="reservation" @selected(old('order_type', $venta->order_type) === 'reservation')>reservation</option>
                </select>
            </div>
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Actualizar Venta</button>
    </form>
</div>
@endsection

