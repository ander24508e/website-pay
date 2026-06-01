@extends('layouts.admin')

@section('title', 'Editar Movimiento')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Editar Movimiento #{{ $movement->id }}</h2>
        <a href="{{ route('admin.inventario.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.inventario.movements.update', $movement) }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <input type="text" value="{{ strtoupper($movement->type) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="text" value="{{ $movement->quantity }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" disabled>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $movement->notes) }}</textarea>
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Actualizar Movimiento</button>
    </form>
</div>
@endsection

