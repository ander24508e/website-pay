<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemVariant;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $variants = CatalogItemVariant::query()
            ->with(['item.type', 'item.category'])
            ->whereHas('item', function ($query) {
                $query->where('uses_inventory', true);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhereHas('item', function ($itemQuery) use ($q) {
                            $itemQuery->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $recentMovements = InventoryMovement::query()
            ->with(['variant.item', 'user'])
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.inventario.index', compact('variants', 'recentMovements'));
    }

    public function create()
    {
        $variants = CatalogItemVariant::query()
            ->with('item')
            ->whereHas('item', fn ($query) => $query->where('uses_inventory', true))
            ->ordered()
            ->get();

        return view('admin.inventario.create', compact('variants'));
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'catalog_item_variant_id' => ['required', 'integer', 'exists:catalog_item_variants,id'],
            'type' => ['required', 'in:in,out,adjust'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $variant = CatalogItemVariant::query()
            ->with('item')
            ->findOrFail($data['catalog_item_variant_id']);

        if (!$variant->item || !$variant->item->uses_inventory) {
            NotificationHelper::error('Este item no tiene inventario habilitado.');
            return redirect()->back();
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
        $variants = CatalogItemVariant::query()
            ->with('item')
            ->whereHas('item', fn ($query) => $query->where('uses_inventory', true))
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
}
