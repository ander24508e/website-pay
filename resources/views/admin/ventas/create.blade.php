@extends('layouts.admin')

@section('title', 'Nueva Venta')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Nueva Venta</h2>
        <a href="{{ route('admin.ventas.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.ventas.store') }}" class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
        @csrf
        <div>
            <div class="flex items-center justify-between gap-3 mb-1">
                <label for="ventaClienteSelect" class="block text-sm font-medium text-gray-700">Cliente</label>
                <button type="button" id="openQuickClientModal" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 transition">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    Nuevo Cliente
                </button>
            </div>
            <select id="ventaClienteSelect" name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Invitado</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @selected(old('user_id') == $cliente->id)>{{ $cliente->name }} ({{ $cliente->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                <input type="number" step="0.01" min="0" name="total" value="{{ old('total', '0.00') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['pending','paid','reserved','failed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(old('status', 'pending') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="order_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="purchase" @selected(old('order_type', 'purchase') === 'purchase')>purchase</option>
                    <option value="reservation" @selected(old('order_type') === 'reservation')>reservation</option>
                </select>
            </div>
        </div>
        <button class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold">Guardar Venta</button>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                        <input type="text" name="telefono" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                        <input type="text" name="direccion" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" id="cancelQuickClient" class="rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" id="saveQuickClient" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const quickClientModal = document.getElementById('quickClientModal');
const quickClientForm = document.getElementById('quickClientForm');
const quickClientErrors = document.getElementById('quickClientErrors');
const ventaClienteSelect = document.getElementById('ventaClienteSelect');
const saveQuickClient = document.getElementById('saveQuickClient');

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

    (messages.length ? messages : ['No se pudo crear el cliente. Revisa los datos.']).forEach((message) => {
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
    if (event.target === quickClientModal) {
        closeQuickClientModal();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !quickClientModal.classList.contains('hidden')) {
        closeQuickClientModal();
    }
});

quickClientForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    quickClientErrors.classList.add('hidden');
    quickClientErrors.innerHTML = '';
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
        const option = new Option(cliente.label, cliente.id, true, true);
        ventaClienteSelect.add(option);
        ventaClienteSelect.value = cliente.id;
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
