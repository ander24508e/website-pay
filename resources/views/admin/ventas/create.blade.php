@extends('layouts.admin')

@section('title', 'Nueva Venta Sistema')

@section('content')
@php
    $catalogPayload = $catalogItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'type' => $item->type?->name ?? 'Catalogo',
        'business_model' => $item->type?->business_model ?? \App\Models\CatalogType::BUSINESS_MODEL_SERVICES,
        'price' => (float) $item->display_price,
        'uses_inventory' => (bool) $item->uses_inventory,
        'variants' => $item->activeVariants->map(fn ($variant) => [
            'id' => $variant->id,
            'label' => trim(($variant->presentation ?: $variant->name) . ($variant->sku ? ' - ' . $variant->sku : '')),
            'price' => (float) ($variant->price ?? $item->display_price),
            'stock' => (int) ($variant->stock ?? 0),
        ])->values(),
        'vehicle_prices' => $item->vehicleTypePrices->pluck('price', 'vehicle_type_id')->map(fn ($price) => (float) $price),
    ])->values();
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Agregar Venta </h2>
            <p class="text-sm text-gray-500">Registra ventas del panel sin mezclar el flujo web de checkout.</p>
        </div>
        <a href="{{ route('admin.ventas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.ventas.store') }}" id="saleForm" class="space-y-5" enctype="multipart/form-data">
        @csrf

        <section class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-4">
            <div>
                <h3 class="font-bold text-gray-800">Datos de la venta</h3>
                <p class="text-xs text-gray-400">Define cliente, veh&iacute;culo, responsable y observaciones internas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="xl:col-span-2">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <button type="button" id="openQuickClientModal" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 transition">
                            <x-heroicon-o-plus class="w-4 h-4" />
                            Nuevo Cliente
                        </button>
                    </div>
                    <select name="user_id" id="clientSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Invitado</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('user_id') == $cliente->id)>{{ $cliente->name }} ({{ $cliente->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehiculo</label>
                    <select name="vehicle_id" id="mainVehicleSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Buscar</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" data-client="{{ $vehicle->user_id }}" data-type="{{ $vehicle->resolvedType()?->id }}">
                                {{ $vehicle->plate }} - {{ $vehicle->resolvedBrand()?->name }} {{ $vehicle->resolvedModel()?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trabajador</label>
                    <select name="attended_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Usuario actual</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected(old('attended_by', auth()->id()) == $usuario->id)>{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="xl:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
            <section class="xl:col-span-3 bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-800">Items de venta</h3>
                        <p class="text-xs text-gray-400">Agrega productos o servicios del cat&aacute;logo.</p>
                    </div>
                    <button type="button" id="addSaleItem" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                        <x-heroicon-o-plus class="w-4 h-4" />
                        Agregar item
                    </button>
                </div>

                <div id="saleItems" class="space-y-4"></div>

                @error('items')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            <aside class="space-y-5 h-fit">
                <section class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-800">Resumen</h3>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><strong id="subtotalText">$0.00</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Descuentos</span><strong id="discountText">$0.00</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Impuestos</span><strong id="taxText">$0.00</strong></div>
                        <div class="flex justify-between border-t border-gray-200 pt-3"><span class="font-semibold text-gray-800">Total</span><strong id="totalText" class="text-2xl text-gray-900">$0.00</strong></div>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-4">
                    <div>
                        <h3 class="font-bold text-gray-800">Pago</h3>
                        <p class="text-xs text-gray-400">El sistema asigna estados autom&aacute;ticamente seg&uacute;n el m&eacute;todo.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">M&eacute;todo de pago</label>
                        <select name="payment[method]" id="paymentMethod" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach(['cash' => 'Efectivo', 'payphone' => 'PayPhone', 'transfer' => 'Transferencia', 'card' => 'Tarjeta', 'credit' => 'Cr&eacute;dito'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('payment.method', 'cash') === $key)>{!! $label !!}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="payment-fields space-y-3" data-payment-fields="cash">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto recibido</label>
                        <input type="number" step="0.01" min="0" name="payment[received_amount]" id="receivedAmount" value="{{ old('payment.received_amount') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <p class="text-sm text-gray-500">Cambio: <strong id="changeText">$0.00</strong></p>
                    </div>

                    <div class="payment-fields hidden space-y-3" data-payment-fields="payphone">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID de transacci&oacute;n PayPhone</label>
                        <input type="text" name="payment[transaction_id]" value="{{ old('payment.transaction_id') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="payment-fields hidden space-y-3" data-payment-fields="transfer">
                        <input type="text" name="payment[bank]" value="{{ old('payment.bank') }}" placeholder="Banco" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <input type="text" name="payment[reference]" value="{{ old('payment.reference') }}" placeholder="Referencia" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <input type="file" name="payment[proof]" accept=".jpg,.jpeg,.png,.webp,.pdf" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="payment-fields hidden space-y-3" data-payment-fields="card">
                        <label class="block text-sm font-medium text-gray-700 mb-1">C&oacute;digo de autorizaci&oacute;n</label>
                        <input type="text" name="payment[authorization_code]" value="{{ old('payment.authorization_code') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="payment-fields hidden space-y-3" data-payment-fields="credit">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
                        <input type="date" name="payment[due_date]" value="{{ old('payment.due_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <textarea name="payment[notes]" rows="2" placeholder="Observaciones" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('payment.notes') }}</textarea>
                    </div>

                    <button class="w-full bg-gray-900 text-white px-5 py-3 rounded-lg text-sm font-semibold hover:bg-gray-700">Guardar Venta</button>
                </section>
            </aside>
        </div>
    </form>

    <div id="quickClientModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 px-4">
        <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Nuevo Cliente</h3>
                    <p class="text-sm text-gray-400">Crea el cliente sin salir de la venta.</p>
                </div>
                <button type="button" id="closeQuickClientModal" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form id="quickClientForm" class="space-y-4 px-6 py-5">
                @csrf
                <div id="quickClientErrors" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Direcci&oacute;n</label>
                        <input type="text" name="direccion" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contrase&ntilde;a</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" id="cancelQuickClient" class="rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">Cancelar</button>
                    <button type="submit" id="saveQuickClient" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="saleItemTemplate">
    <div class="sale-item-row rounded-xl border border-gray-100 bg-gray-50 p-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Producto/Servicio</label>
                <select data-field="catalog_item_id" class="catalog-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Selecciona un item</option>
                    @foreach($catalogItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->type?->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Presentaci&oacute;n</label>
                <select data-field="catalog_item_variant_id" class="variant-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">General</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Veh&iacute;culo</label>
                <select data-field="vehicle_id" class="vehicle-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin veh&iacute;culo</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-client="{{ $vehicle->user_id }}" data-type="{{ $vehicle->resolvedType()?->id }}">
                            {{ $vehicle->plate }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tipo veh&iacute;culo</label>
                <select data-field="vehicle_type_id" class="vehicle-type-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin tipo</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cant.</label>
                <input type="number" data-field="quantity" value="1" min="1" class="quantity-input w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">P. unit.</label>
                <div class="unit-price w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-800">$0.00</div>
            </div>
            <div class="md:col-span-1 flex items-end justify-end">
                <button type="button" class="remove-sale-item inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">
                    <x-heroicon-o-trash class="w-5 h-5" />
                </button>
            </div>
        </div>
        <div class="mt-3 text-right text-sm text-gray-500">Subtotal l&iacute;nea: <strong class="line-subtotal text-gray-800">$0.00</strong></div>
    </div>
</template>
@endsection

@push('scripts')
<script>
const catalogItems = @json($catalogPayload);
const saleItems = document.getElementById('saleItems');
const template = document.getElementById('saleItemTemplate');
const quickClientModal = document.getElementById('quickClientModal');
const quickClientForm = document.getElementById('quickClientForm');
const quickClientErrors = document.getElementById('quickClientErrors');
const clientSelect = document.getElementById('clientSelect');
const saveQuickClient = document.getElementById('saveQuickClient');
let itemIndex = 0;

function money(value) {
    return '$' + Number(value || 0).toFixed(2);
}

function itemById(id) {
    return catalogItems.find((item) => String(item.id) === String(id));
}

function refreshNames(row) {
    row.querySelectorAll('[data-field]').forEach((field) => {
        field.name = `items[${row.dataset.index}][${field.dataset.field}]`;
    });
}

function fillVariants(row, item) {
    const variantSelect = row.querySelector('.variant-select');
    variantSelect.innerHTML = '<option value="">General</option>';
    (item?.variants || []).forEach((variant) => {
        const option = new Option(`${variant.label || 'General'} (${money(variant.price)} / Stock ${variant.stock})`, variant.id);
        option.dataset.price = variant.price;
        option.dataset.stock = variant.stock;
        variantSelect.add(option);
    });
}

function linePrice(row) {
    const item = itemById(row.querySelector('.catalog-select')?.value);
    if (!item) return 0;

    const variantOption = row.querySelector('.variant-select')?.selectedOptions?.[0];
    if (variantOption?.value) {
        return Number(variantOption.dataset.price || item.price || 0);
    }

    const vehicleTypeId = row.querySelector('.vehicle-type-select')?.value;
    if (vehicleTypeId && item.vehicle_prices && item.vehicle_prices[vehicleTypeId] !== undefined) {
        return Number(item.vehicle_prices[vehicleTypeId]);
    }

    return Number(item.price || 0);
}

function recalculate() {
    let subtotal = 0;
    saleItems.querySelectorAll('.sale-item-row').forEach((row) => {
        const quantity = Math.max(1, Number(row.querySelector('.quantity-input')?.value || 1));
        const unitPrice = linePrice(row);
        const lineSubtotal = unitPrice * quantity;
        row.querySelector('.unit-price').textContent = money(unitPrice);
        row.querySelector('.line-subtotal').textContent = money(lineSubtotal);
        subtotal += lineSubtotal;
    });

    const discount = 0;
    const tax = 0;
    const total = subtotal - discount + tax;

    document.getElementById('subtotalText').textContent = money(subtotal);
    document.getElementById('discountText').textContent = money(discount);
    document.getElementById('taxText').textContent = money(tax);
    document.getElementById('totalText').textContent = money(total);
    refreshPaymentFields();
}

function optionMatchesClient(option, clientId) {
    return !clientId || !option.dataset.client || option.dataset.client === clientId;
}

function filterVehicleSelect(select, clientId) {
    Array.from(select.options).forEach((option) => {
        if (!option.value) return;
        const visible = optionMatchesClient(option, clientId);
        option.hidden = !visible;
        option.disabled = !visible;
    });

    if (select.value && select.selectedOptions[0]?.disabled) {
        select.value = '';
    }
}

function filterVehiclesByClient() {
    const clientId = clientSelect?.value || '';
    const mainVehicleSelect = document.getElementById('mainVehicleSelect');
    if (mainVehicleSelect) {
        filterVehicleSelect(mainVehicleSelect, clientId);
    }

    saleItems.querySelectorAll('.vehicle-select').forEach((select) => {
        filterVehicleSelect(select, clientId);
    });
}

function refreshPaymentFields() {
    const method = document.getElementById('paymentMethod')?.value || 'cash';
    document.querySelectorAll('.payment-fields').forEach((section) => {
        section.classList.toggle('hidden', section.dataset.paymentFields !== method);
    });

    const total = Number((document.getElementById('totalText')?.textContent || '$0').replace('$', '')) || 0;
    const received = Number(document.getElementById('receivedAmount')?.value || 0);
    const change = Math.max(0, received - total);
    const changeText = document.getElementById('changeText');
    if (changeText) {
        changeText.textContent = money(change);
    }
}

function addRow() {
    const fragment = template.content.cloneNode(true);
    const row = fragment.querySelector('.sale-item-row');
    row.dataset.index = itemIndex++;
    refreshNames(row);
    saleItems.appendChild(fragment);
    filterVehiclesByClient();
    recalculate();
}

document.getElementById('addSaleItem')?.addEventListener('click', addRow);

saleItems?.addEventListener('change', (event) => {
    const row = event.target.closest('.sale-item-row');
    if (!row) return;

    if (event.target.classList.contains('catalog-select')) {
        fillVariants(row, itemById(event.target.value));
    }

    if (event.target.classList.contains('vehicle-select')) {
        const typeId = event.target.selectedOptions?.[0]?.dataset?.type;
        if (typeId) {
            row.querySelector('.vehicle-type-select').value = typeId;
        }
    }

    recalculate();
});

saleItems?.addEventListener('input', recalculate);
saleItems?.addEventListener('click', (event) => {
    const button = event.target.closest('.remove-sale-item');
    if (!button) return;
    button.closest('.sale-item-row')?.remove();
    recalculate();
});

addRow();

document.getElementById('paymentMethod')?.addEventListener('change', refreshPaymentFields);
document.getElementById('receivedAmount')?.addEventListener('input', refreshPaymentFields);
clientSelect?.addEventListener('change', () => {
    filterVehiclesByClient();
    recalculate();
});
document.getElementById('mainVehicleSelect')?.addEventListener('change', (event) => {
    const typeId = event.target.selectedOptions?.[0]?.dataset?.type;
    if (!typeId) return;

    saleItems.querySelectorAll('.sale-item-row').forEach((row) => {
        if (!row.querySelector('.vehicle-select')?.value) {
            row.querySelector('.vehicle-type-select').value = typeId;
        }
    });
    recalculate();
});
filterVehiclesByClient();
refreshPaymentFields();

function openQuickClientModal() {
    quickClientModal.classList.remove('hidden');
    quickClientModal.classList.add('flex');
    quickClientForm.querySelector('input[name="name"]')?.focus();
}

function closeQuickClientModal() {
    quickClientModal.classList.add('hidden');
    quickClientModal.classList.remove('flex');
    quickClientErrors.classList.add('hidden');
    quickClientErrors.innerHTML = '';
    quickClientForm.reset();
}

function showQuickClientErrors(errors) {
    const messages = Object.values(errors || {}).flat();
    quickClientErrors.innerHTML = '';
    (messages.length ? messages : ['No se pudo crear el cliente.']).forEach((message) => {
        const paragraph = document.createElement('p');
        paragraph.textContent = message;
        quickClientErrors.appendChild(paragraph);
    });
    quickClientErrors.classList.remove('hidden');
}

document.getElementById('openQuickClientModal')?.addEventListener('click', openQuickClientModal);
document.getElementById('closeQuickClientModal')?.addEventListener('click', closeQuickClientModal);
document.getElementById('cancelQuickClient')?.addEventListener('click', closeQuickClientModal);
quickClientModal?.addEventListener('click', (event) => {
    if (event.target === quickClientModal) closeQuickClientModal();
});

quickClientForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    quickClientErrors.classList.add('hidden');
    saveQuickClient.disabled = true;
    saveQuickClient.textContent = 'Guardando...';

    try {
        const response = await fetch(@json(route('admin.clientes.quick-store')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(quickClientForm),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showQuickClientErrors(data.errors || {});
            return;
        }

        const cliente = data.cliente;
        clientSelect.add(new Option(cliente.label, cliente.id, true, true));
        clientSelect.value = cliente.id;
        filterVehiclesByClient();
        closeQuickClientModal();
    } catch (error) {
        showQuickClientErrors({});
    } finally {
        saveQuickClient.disabled = false;
        saveQuickClient.textContent = 'Guardar Cliente';
    }
});
</script>
@endpush

