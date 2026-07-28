@extends('layouts.admin')

@section('title', 'Importar Inventario')

@section('content')
<div class="mx-auto w-full max-w-5xl overflow-x-hidden px-3 pb-4 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventario.index') }}"
               class="flex items-center justify-center w-9 h-9 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition text-gray-500 hover:text-gray-800">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Importar Inventario</h2>
                <p class="text-gray-500 text-sm mt-1">Actualiza stock, mínimos, precios y costos usando un CSV compatible con Excel.</p>
            </div>
        </div>
        <a href="{{ route('admin.inventario.export') }}"
           class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition text-sm font-semibold">
            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
            Descargar plantilla
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
        <form action="{{ route('admin.inventario.import.preview') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 sm:items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo CSV</label>
                <input type="file" name="inventory_file" accept=".csv,text/csv"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm bg-gray-50 @error('inventory_file') border-red-400 bg-red-50 @enderror" required>
                @error('inventory_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition text-sm font-semibold">
                <x-heroicon-o-eye class="w-5 h-5" />
                Vista previa
            </button>
        </form>
    </div>

    @if($previewRows->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-gray-800">Vista previa</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Solo se actualizan SKUs existentes. Los cambios de stock generan movimiento de ajuste.</p>
                </div>
                @unless($hasErrors)
                    <form action="{{ route('admin.inventario.import.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="rows" value="{{ e(json_encode($rawRows)) }}">
                        <button class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition text-sm font-semibold">
                            <x-heroicon-o-check class="w-5 h-5" />
                            Confirmar importación
                        </button>
                    </form>
                @endunless
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Línea</th>
                            <th class="px-3 py-3 text-center">SKU</th>
                            <th class="px-3 py-3 text-center">Producto</th>
                            <th class="px-3 py-3 text-center">Stock</th>
                            <th class="px-3 py-3 text-center">Mínimo</th>
                            <th class="px-3 py-3 text-center">Precio</th>
                            <th class="px-3 py-3 text-center">Costo</th>
                            <th class="px-3 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($previewRows as $row)
                            <tr>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $row['line'] }}</td>
                                <td class="px-3 py-3 text-center font-mono text-xs text-gray-700">{{ $row['sku'] ?: '-' }}</td>
                                <td class="px-3 py-3 text-center text-gray-700">
                                    <p class="font-medium">{{ $row['product'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $row['presentation'] ?? '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center">{{ $row['current_stock'] ?? '-' }} -> {{ $row['stock'] ?? 'sin cambio' }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['min_stock'] ?? 'sin cambio' }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['price'] !== null ? '$' . number_format($row['price'], 2) : 'sin cambio' }}</td>
                                <td class="px-3 py-3 text-center">{{ $row['cost_price'] !== null ? '$' . number_format($row['cost_price'], 2) : 'sin cambio' }}</td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['valid'])
                                        <span class="inline-flex bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">Listo</span>
                                    @else
                                        <span class="inline-flex bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">{{ implode(', ', $row['errors']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
