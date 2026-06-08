<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use App\Models\CatalogType;
use App\Models\CatalogItemVariant;
use App\Models\Empresa;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->getOrCreateEmpresa();
        $q = trim((string) $request->query('q', ''));
        $selectedTypeId = (int) $request->query('catalog_type_id', 0);

        $productTypes = CatalogType::query()
            ->where('empresa_id', $empresa->id)
            ->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS)
            ->ordered()
            ->get();

        if ($selectedTypeId > 0 && !$productTypes->contains('id', $selectedTypeId)) {
            $selectedTypeId = 0;
        }

        $products = CatalogItem::query()
            ->with(['type', 'category', 'variants'])
            ->where('empresa_id', $empresa->id)
            ->whereHas('type', function ($typeQuery) use ($selectedTypeId) {
                $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS)
                    ->when($selectedTypeId > 0, function ($filteredTypeQuery) use ($selectedTypeId) {
                        $filteredTypeQuery->whereKey($selectedTypeId);
                    });
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhereHas('type', function ($typeQuery) use ($q) {
                            $typeQuery->where('name', 'like', "%{$q}%");
                        })
                        ->orWhereHas('category', function ($categoryQuery) use ($q) {
                            $categoryQuery->where('name', 'like', "%{$q}%");
                        })
                        ->orWhereHas('variants', function ($variantQuery) use ($q) {
                            $variantQuery->where('name', 'like', "%{$q}%")
                                ->orWhere('presentation', 'like', "%{$q}%")
                                ->orWhere('specification', 'like', "%{$q}%")
                                ->orWhere('sku', 'like', "%{$q}%");
                        });
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $recentMovements = InventoryMovement::query()
            ->with(['variant.item.type', 'user'])
            ->whereHas('variant.item', function ($itemQuery) use ($empresa, $selectedTypeId) {
                $itemQuery->where('empresa_id', $empresa->id)
                    ->whereHas('type', function ($typeQuery) use ($selectedTypeId) {
                        $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS)
                            ->when($selectedTypeId > 0, function ($filteredTypeQuery) use ($selectedTypeId) {
                                $filteredTypeQuery->whereKey($selectedTypeId);
                            });
                    });
            })
            ->latest()
            ->limit(20)
            ->get();

        $selectedType = $productTypes->firstWhere('id', $selectedTypeId);

        return view('admin.inventario.index', compact('products', 'recentMovements', 'productTypes', 'selectedTypeId', 'selectedType'));
    }

    public function create()
    {
        $empresa = $this->getOrCreateEmpresa();

        $variants = CatalogItemVariant::query()
            ->with('item')
            ->whereHas('item', function ($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id);
                $query->whereHas('type', function ($typeQuery) {
                    $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
                });
            })
            ->ordered()
            ->get();

        return view('admin.inventario.create', compact('variants'));
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'catalog_item_id' => ['nullable', 'required_without:catalog_item_variant_id', 'integer', 'exists:catalog_items,id'],
            'catalog_item_variant_id' => ['nullable', 'required_without:catalog_item_id', 'integer', 'exists:catalog_item_variants,id'],
            'type' => ['required', 'in:in,out,adjust'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $variant = $this->resolveInventoryVariant($data['catalog_item_variant_id'] ?? null, $data['catalog_item_id'] ?? null);

        if (
            !$variant->item
            || ($variant->item->type?->business_model !== CatalogType::BUSINESS_MODEL_PRODUCTS)
        ) {
            NotificationHelper::error('Este producto no tiene inventario habilitado.');
            return redirect()->back();
        }

        if (!$variant->item->uses_inventory) {
            $variant->item->update(['uses_inventory' => true]);
        }

        DB::transaction(function () use ($data, $variant) {
            $currentStock = (int) ($variant->stock ?? 0);
            $quantity = (int) $data['quantity'];

            if ($data['type'] === 'in') {
                $nextStock = $currentStock + $quantity;
            } elseif ($data['type'] === 'out') {
                $nextStock = max(0, $currentStock - $quantity);
            } else {
                $nextStock = $quantity;
            }

            $variant->update(['stock' => $nextStock]);

            InventoryMovement::create([
                'catalog_item_variant_id' => $variant->id,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'quantity' => $quantity,
                'stock_before' => $currentStock,
                'stock_after' => $nextStock,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
            ]);
        });

        NotificationHelper::success('Movimiento de inventario registrado.');

        return redirect()->route('admin.inventario.index');
    }

    public function edit(InventoryMovement $movement)
    {
        $empresa = $this->getOrCreateEmpresa();

        $variants = CatalogItemVariant::query()
            ->with('item')
            ->whereHas('item', function ($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id);
                $query->whereHas('type', function ($typeQuery) {
                    $typeQuery->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
                });
            })
            ->ordered()
            ->get();

        return view('admin.inventario.edit', compact('movement', 'variants'));
    }

    public function update(Request $request, InventoryMovement $movement)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $movement->update([
            'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
        ]);

        NotificationHelper::success('Movimiento actualizado.');
        return redirect()->route('admin.inventario.index');
    }

    public function destroy(InventoryMovement $movement)
    {
        $movement->delete();
        NotificationHelper::success('Movimiento eliminado.');
        return redirect()->route('admin.inventario.index');
    }

    private function resolveInventoryVariant(?int $variantId, ?int $itemId): CatalogItemVariant
    {
        $empresa = $this->getOrCreateEmpresa();

        if ($variantId) {
            return CatalogItemVariant::query()
                ->with('item.type')
                ->whereHas('item', function ($query) use ($empresa) {
                    $query->where('empresa_id', $empresa->id);
                })
                ->findOrFail($variantId);
        }

        $item = CatalogItem::query()
            ->with(['type', 'variants'])
            ->where('empresa_id', $empresa->id)
            ->whereHas('type', function ($query) {
                $query->where('business_model', CatalogType::BUSINESS_MODEL_PRODUCTS);
            })
            ->findOrFail($itemId);

        if (!$item->uses_inventory) {
            $item->update(['uses_inventory' => true]);
        }

        $variant = $item->variants->first();

        if ($variant) {
            $variant->load('item.type');
            return $variant;
        }

        return CatalogItemVariant::create([
            'catalog_item_id' => $item->id,
            'name' => 'General',
            'price' => $item->base_price,
            'stock' => 0,
            'active' => true,
            'is_default' => true,
            'sort_order' => 0,
        ])->load('item.type');
    }

    private function getOrCreateEmpresa(): Empresa
    {
        return Empresa::query()->first() ?? Empresa::create([
            'nombre' => 'Mi negocio',
        ]);
    }
}
