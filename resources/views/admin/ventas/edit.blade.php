@extends('layouts.admin')

@section('title', 'Editar Venta Sistema')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Venta Sistema #{{ $sale->id }}</h2>
            <p class="text-sm text-gray-500">Solo se editan datos generales mientras la venta no est&eacute; pagada.</p>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Veh&iacute;culo</label>
                <select name="vehicle_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin veh&iacute;culo</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-client="{{ $vehicle->user_id }}" @selected(old('vehicle_id', $sale->vehicle_id) == $vehicle->id)>{{ $vehicle->plate }} - {{ $vehicle->resolvedBrand()?->name }} {{ $vehicle->resolvedModel()?->name }}</option>
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

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $sale->notes) }}</textarea>
        </div>

        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Actualizar Venta</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const editClientSelect = document.querySelector('select[name="user_id"]');
const editVehicleSelect = document.querySelector('select[name="vehicle_id"]');

function filterEditVehicles() {
    const clientId = editClientSelect?.value || '';
    Array.from(editVehicleSelect?.options || []).forEach((option) => {
        if (!option.value) return;
        const visible = !clientId || !option.dataset.client || option.dataset.client === clientId;
        option.hidden = !visible;
        option.disabled = !visible;
    });

    if (editVehicleSelect?.value && editVehicleSelect.selectedOptions[0]?.disabled) {
        editVehicleSelect.value = '';
    }
}

editClientSelect?.addEventListener('change', filterEditVehicles);
filterEditVehicles();
</script>
@endpush
