@extends('layouts.admin')

@section('title', 'Editar Venta Sistema')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Venta Sistema #{{ $sale->id }}</h2>
            <p class="text-sm text-gray-500">Solo se editan datos generales mientras la venta no esté pagada.</p>
        </div>
        <a href="{{ route('admin.ventas.show', 'internal-' . $sale->id) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.ventas.update', 'internal-' . $sale->id) }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Invitado</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected(old('user_id', $sale->user_id) == $cliente->id)>{{ $cliente->name }} ({{ $cliente->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vehículo</label>
                <select name="vehicle_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin vehículo</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $sale->vehicle_id) == $vehicle->id)>{{ $vehicle->plate }} - {{ $vehicle->resolvedBrand()?->name }} {{ $vehicle->resolvedModel()?->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Atendido por</label>
                <select name="attended_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin asignar</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected(old('attended_by', $sale->attended_by) == $usuario->id)>{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="pending" @selected(old('status', $sale->status) === 'pending')>Pendiente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pago</label>
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="pending" @selected(old('payment_status', $sale->payment_status) === 'pending')>Pendiente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'card' => 'Tarjeta', 'other' => 'Otro'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('payment_method', $sale->payment_method) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $sale->notes) }}</textarea>
        </div>

        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Actualizar Venta</button>
    </form>
</div>
@endsection
