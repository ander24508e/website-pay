@extends('layouts.admin')

@section('title', 'Nuevo Movimiento')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Nuevo Movimiento</h2>
        <a href="{{ route('admin.inventario.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.inventario.movements.store') }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Variante</label>
            <select name="catalog_item_variant_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                @foreach($variants as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->item->name ?? '-' }} - {{ $variant->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="in">Entrada</option>
                    <option value="out">Salida</option>
                    <option value="adjust">Ajustar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input type="text" name="notes" value="{{ old('notes') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Guardar Movimiento</button>
    </form>
</div>
@endsection

