<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Empresa;
use App\Models\InventoryLocation;
use App\Models\InventoryMovement;
use App\Models\InventoryCount;
use App\Models\InventoryReturn;
use App\Models\InventoryStock;
use App\Models\InventoryTransfer;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryOperationsController extends Controller
{
    public function locations()
    {
        $empresa = $this->getOrCreateEmpresa();
        $locations = InventoryLocation::query()
            ->where('empresa_id', $empresa->id)
            ->withCount('stocks')
            ->ordered()
            ->paginate(15);

        return view('admin.inventario.locations', compact('locations'));
    }

    public function storeLocation(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['warehouse', 'branch', 'vehicle', 'other'])],
            'address' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($empresa, $data, $request) {
            if ($request->boolean('is_default')) {
                InventoryLocation::query()->where('empresa_id', $empresa->id)->update(['is_default' => false]);
            }

            InventoryLocation::create([
                'empresa_id' => $empresa->id,
                'name' => trim($data['name']),
                'type' => $data['type'],
                'address' => $this->cleanInput($data['address'] ?? null),
                'is_default' => $request->boolean('is_default'),
                'active' => $request->boolean('active', true),
            ]);
        });

        NotificationHelper::success('Ubicacion creada correctamente.');
        return redirect()->route('admin.inventario.locations');
    }

    public function suppliers()
    {
        $empresa = $this->getOrCreateEmpresa();
        $suppliers = Supplier::query()
            ->where('empresa_id', $empresa->id)
            ->withCount('purchases')
            ->ordered()
            ->paginate(15);

        return view('admin.inventario.suppliers', compact('suppliers'));
    }

    public function storeSupplier(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        Supplier::create([
            'empresa_id' => $empresa->id,
            'name' => trim($data['name']),
            'document' => $this->cleanInput($data['document'] ?? null),
            'phone' => $this->cleanInput($data['phone'] ?? null),
            'email' => $this->cleanInput($data['email'] ?? null),
            'address' => $this->cleanInput($data['address'] ?? null),
            'notes' => $this->cleanInput($data['notes'] ?? null),
            'active' => $request->boolean('active', true),
        ]);

        NotificationHelper::success('Proveedor creado correctamente.');
        return redirect()->route('admin.inventario.suppliers');
    }

    public function purchases()
    {
        $empresa = $this->getOrCreateEmpresa();
        $purchases = Purchase::query()
            ->where('empresa_id', $empresa->id)
            ->with(['supplier', 'location', 'items.variant.item'])
            ->latest()
            ->paginate(15);
        $suppliers = Supplier::query()->where('empresa_id', $empresa->id)->active()->ordered()->get();
        $locations = $this->activeLocations($empresa);
        $variants = $this->inventoryVariants($empresa);

        return view('admin.inventario.purchases', compact('purchases', 'suppliers', 'locations', 'variants'));
    }

    public function storePurchase(Request $request, InventoryService $inventoryService)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'inventory_location_id' => ['required', 'integer', 'exists:inventory_locations,id'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $location = InventoryLocation::query()
            ->where('empresa_id', $empresa->id)
            ->findOrFail($data['inventory_location_id']);

        DB::transaction(function () use ($empresa, $data, $location, $inventoryService) {
            $items = collect($data['items'])
                ->filter(fn ($item) => !empty($item['catalog_item_variant_id']) && (int) ($item['quantity'] ?? 0) > 0);

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['items' => 'Agrega al menos un producto a la compra.']);
            }

            $total = $items->sum(fn ($item) => round((float) $item['unit_cost'] * (int) $item['quantity'], 2));

            $purchase = Purchase::create([
                'empresa_id' => $empresa->id,
                'supplier_id' => $data['supplier_id'] ?? null,
                'inventory_location_id' => $location->id,
                'user_id' => auth()->id(),
                'document_number' => $this->cleanInput($data['document_number'] ?? null),
                'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                'status' => 'received',
                'subtotal' => $total,
                'total' => $total,
                'notes' => $this->cleanInput($data['notes'] ?? null),
            ]);

            foreach ($items as $row) {
                $variant = CatalogItemVariant::query()->findOrFail($row['catalog_item_variant_id']);
                $quantity = (int) $row['quantity'];
                $unitCost = round((float) $row['unit_cost'], 2);

                $purchaseItem = $purchase->items()->create([
                    'catalog_item_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => round($unitCost * $quantity, 2),
                ]);

                $inventoryService->applyMovement(
                    $variant,
                    'in',
                    $quantity,
                    'Compra #' . $purchase->id,
                    [
                        'inventory_location_id' => $location->id,
                        'purchase_id' => $purchase->id,
                        'purchase_item_id' => $purchaseItem->id,
                        'reason' => 'compra',
                        'reference' => $purchase->document_number ?: 'purchase:' . $purchase->id,
                        'unit_cost' => $unitCost,
                    ]
                );
            }
        });

        NotificationHelper::success('Compra registrada e inventario actualizado.');
        return redirect()->route('admin.inventario.purchases');
    }

    public function transfers()
    {
        $empresa = $this->getOrCreateEmpresa();
        $transfers = InventoryTransfer::query()
            ->where('empresa_id', $empresa->id)
            ->with(['fromLocation', 'toLocation', 'items.variant.item'])
            ->latest()
            ->paginate(15);
        $locations = $this->activeLocations($empresa);
        $variants = $this->inventoryVariants($empresa);

        return view('admin.inventario.transfers', compact('transfers', 'locations', 'variants'));
    }

    public function storeTransfer(Request $request, InventoryService $inventoryService)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'from_location_id' => ['required', 'integer', 'exists:inventory_locations,id'],
            'to_location_id' => ['required', 'integer', 'exists:inventory_locations,id', 'different:from_location_id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $fromLocation = InventoryLocation::query()->where('empresa_id', $empresa->id)->findOrFail($data['from_location_id']);
        $toLocation = InventoryLocation::query()->where('empresa_id', $empresa->id)->findOrFail($data['to_location_id']);

        DB::transaction(function () use ($empresa, $data, $fromLocation, $toLocation, $inventoryService) {
            $transfer = InventoryTransfer::create([
                'empresa_id' => $empresa->id,
                'from_location_id' => $fromLocation->id,
                'to_location_id' => $toLocation->id,
                'user_id' => auth()->id(),
                'reference' => $this->cleanInput($data['reference'] ?? null),
                'status' => 'completed',
                'notes' => $this->cleanInput($data['notes'] ?? null),
            ]);

            $rows = collect($data['items'])
                ->filter(fn ($item) => !empty($item['catalog_item_variant_id']) && (int) ($item['quantity'] ?? 0) > 0);

            if ($rows->isEmpty()) {
                throw ValidationException::withMessages(['items' => 'Agrega al menos un producto a transferir.']);
            }

            foreach ($rows as $row) {
                $item = $transfer->items()->create([
                    'catalog_item_variant_id' => (int) $row['catalog_item_variant_id'],
                    'quantity' => (int) $row['quantity'],
                ]);

                $inventoryService->transfer($transfer, $item, $fromLocation, $toLocation);
            }
        });

        NotificationHelper::success('Transferencia registrada correctamente.');
        return redirect()->route('admin.inventario.transfers');
    }

    public function kardex(Request $request, CatalogItemVariant $variant)
    {
        $variant->load('item.type', 'item.category');

        $movements = InventoryMovement::query()
            ->with(['location', 'fromLocation', 'toLocation', 'user'])
            ->where('catalog_item_variant_id', $variant->id)
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->query('date_to')))
            ->oldest()
            ->paginate(50)
            ->withQueryString();

        $stocks = InventoryStock::query()
            ->with('location')
            ->where('catalog_item_variant_id', $variant->id)
            ->get();

        return view('admin.inventario.kardex', compact('variant', 'movements', 'stocks'));
    }

    public function returns()
    {
        $empresa = $this->getOrCreateEmpresa();
        $returns = InventoryReturn::query()
            ->where('empresa_id', $empresa->id)
            ->with(['location', 'supplier', 'items.variant.item'])
            ->latest()
            ->paginate(15);
        $locations = $this->activeLocations($empresa);
        $suppliers = Supplier::query()->where('empresa_id', $empresa->id)->active()->ordered()->get();
        $variants = $this->inventoryVariants($empresa);

        return view('admin.inventario.returns', compact('returns', 'locations', 'suppliers', 'variants'));
    }

    public function storeReturn(Request $request, InventoryService $inventoryService)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'type' => ['required', Rule::in(['customer', 'supplier'])],
            'inventory_location_id' => ['required', 'integer', 'exists:inventory_locations,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $location = InventoryLocation::query()->where('empresa_id', $empresa->id)->findOrFail($data['inventory_location_id']);

        DB::transaction(function () use ($empresa, $data, $location, $inventoryService) {
            $rows = collect($data['items'])
                ->filter(fn ($item) => !empty($item['catalog_item_variant_id']) && (int) ($item['quantity'] ?? 0) > 0);

            if ($rows->isEmpty()) {
                throw ValidationException::withMessages(['items' => 'Agrega al menos un producto a devolver.']);
            }

            $return = InventoryReturn::create([
                'empresa_id' => $empresa->id,
                'inventory_location_id' => $location->id,
                'supplier_id' => $data['type'] === 'supplier' ? ($data['supplier_id'] ?? null) : null,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'reference' => $this->cleanInput($data['reference'] ?? null),
                'status' => 'completed',
                'notes' => $this->cleanInput($data['notes'] ?? null),
            ]);

            foreach ($rows as $row) {
                $variant = CatalogItemVariant::query()->findOrFail($row['catalog_item_variant_id']);
                $unitCost = isset($row['unit_cost']) && $row['unit_cost'] !== null && $row['unit_cost'] !== ''
                    ? round((float) $row['unit_cost'], 2)
                    : (float) ($variant->cost_price ?? 0);
                $quantity = (int) $row['quantity'];

                $returnItem = $return->items()->create([
                    'catalog_item_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                ]);

                $inventoryService->applyMovement(
                    $variant,
                    $data['type'] === 'customer' ? 'in' : 'out',
                    $quantity,
                    ($data['type'] === 'customer' ? 'Devolucion cliente #' : 'Devolucion proveedor #') . $return->id,
                    [
                        'inventory_location_id' => $location->id,
                        'inventory_return_id' => $return->id,
                        'inventory_return_item_id' => $returnItem->id,
                        'reason' => $data['type'] === 'customer' ? 'devolucion_cliente' : 'devolucion_proveedor',
                        'reference' => $return->reference ?: 'return:' . $return->id,
                        'unit_cost' => $unitCost,
                    ]
                );
            }
        });

        NotificationHelper::success('Devolucion registrada correctamente.');
        return redirect()->route('admin.inventario.returns');
    }

    public function counts()
    {
        $empresa = $this->getOrCreateEmpresa();
        $counts = InventoryCount::query()
            ->where('empresa_id', $empresa->id)
            ->with(['location', 'items.variant.item'])
            ->latest()
            ->paginate(15);
        $locations = $this->activeLocations($empresa);
        $variants = $this->inventoryVariants($empresa);

        return view('admin.inventario.counts', compact('counts', 'locations', 'variants'));
    }

    public function storeCount(Request $request, InventoryService $inventoryService)
    {
        $empresa = $this->getOrCreateEmpresa();
        $data = $request->validate([
            'inventory_location_id' => ['nullable', 'integer', 'exists:inventory_locations,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_item_variant_id' => ['nullable', 'integer', 'exists:catalog_item_variants,id'],
            'items.*.counted_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        $location = !empty($data['inventory_location_id'])
            ? InventoryLocation::query()->where('empresa_id', $empresa->id)->findOrFail($data['inventory_location_id'])
            : null;

        DB::transaction(function () use ($empresa, $data, $location, $inventoryService) {
            $rows = collect($data['items'])
                ->filter(fn ($item) => !empty($item['catalog_item_variant_id']) && $item['counted_quantity'] !== null && $item['counted_quantity'] !== '');

            if ($rows->isEmpty()) {
                throw ValidationException::withMessages(['items' => 'Agrega al menos un producto contado.']);
            }

            $count = InventoryCount::create([
                'empresa_id' => $empresa->id,
                'inventory_location_id' => $location?->id,
                'user_id' => auth()->id(),
                'reference' => $this->cleanInput($data['reference'] ?? null),
                'status' => 'completed',
                'notes' => $this->cleanInput($data['notes'] ?? null),
            ]);

            foreach ($rows as $row) {
                $variant = CatalogItemVariant::query()->findOrFail($row['catalog_item_variant_id']);
                $expected = $location
                    ? (int) InventoryStock::query()
                        ->where('inventory_location_id', $location->id)
                        ->where('catalog_item_variant_id', $variant->id)
                        ->value('quantity')
                    : (int) ($variant->stock ?? 0);
                $counted = (int) $row['counted_quantity'];
                $difference = $counted - $expected;

                $countItem = $count->items()->create([
                    'catalog_item_variant_id' => $variant->id,
                    'expected_quantity' => $expected,
                    'counted_quantity' => $counted,
                    'difference_quantity' => $difference,
                ]);

                if ($difference === 0) {
                    continue;
                }

                $inventoryService->applyMovement(
                    $variant,
                    $difference > 0 ? 'in' : 'out',
                    abs($difference),
                    'Conteo fisico #' . $count->id,
                    [
                        'inventory_location_id' => $location?->id,
                        'inventory_count_id' => $count->id,
                        'inventory_count_item_id' => $countItem->id,
                        'reason' => 'conteo_fisico',
                        'reference' => $count->reference ?: 'count:' . $count->id,
                        'unit_cost' => $variant->cost_price,
                    ]
                );
            }
        });

        NotificationHelper::success('Conteo fisico registrado y diferencias ajustadas.');
        return redirect()->route('admin.inventario.counts');
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create(['nombre' => 'Mi negocio']);
    }

    private function activeLocations(Empresa $empresa)
    {
        $locations = InventoryLocation::query()->where('empresa_id', $empresa->id)->active()->ordered()->get();

        if ($locations->isNotEmpty()) {
            return $locations;
        }

        return collect([InventoryLocation::create([
            'empresa_id' => $empresa->id,
            'name' => 'Bodega principal',
            'type' => 'warehouse',
            'is_default' => true,
            'active' => true,
        ])]);
    }

    private function inventoryVariants(Empresa $empresa)
    {
        return CatalogItemVariant::query()
            ->with('item')
            ->whereHas('item', function ($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id)
                    ->whereHas('type', fn ($typeQuery) => $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS));
            })
            ->ordered()
            ->get();
    }

    private function cleanInput(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
