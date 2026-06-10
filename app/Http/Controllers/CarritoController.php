<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = session()->get('carrito', []);
        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('carrito.index', compact('carrito', 'total'));
    }

    public function agregar(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'type'     => 'required|in:catalog',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer',
        ]);

        $item = CatalogItem::where('active', true)->where('purchasable', true)->findOrFail($request->id);

        $carrito = session()->get('carrito', []);
        $key = $request->type . '_' . $request->id;

        $variantId = null;
        $variantLabel = null;
        $price = (float) $item->display_price;

        if ($request->filled('variant_id')) {
            $variant = CatalogItemVariant::query()
                ->where('catalog_item_id', $item->id)
                ->where('active', true)
                ->find($request->variant_id);
        } else {
            $variantQuery = $item->activeVariants()
                ->when($item->uses_inventory, fn ($query) => $query->where('stock', '>', 0))
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('price');
            $variant = $variantQuery->first();
        }

        if ($variant) {
            $variantId = $variant->id;
            $variantLabel = trim(($variant->presentation ?? '') . ' ' . ($variant->specification ?? ''));
            $price = (float) ($variant->price ?? $item->display_price);
        }

        $key = $request->type . '_' . $request->id . ($variantId ? ('_v' . $variantId) : '');
        $requestedQuantity = (int) $request->quantity;
        $currentQuantity = (int) ($carrito[$key]['quantity'] ?? 0);

        if ($item->uses_inventory) {
            if (!$variant) {
                return $this->cartError($request, 'Este producto no tiene una presentación inventariable disponible.');
            }

            $availableStock = max(0, (int) ($variant->stock ?? 0));

            if ($availableStock <= 0) {
                return $this->cartError($request, 'Este producto está agotado.');
            }

            if (($currentQuantity + $requestedQuantity) > $availableStock) {
                $remaining = max(0, $availableStock - $currentQuantity);
                $message = $remaining > 0
                    ? "Solo puedes agregar {$remaining} unidad(es) más de este producto."
                    : 'Ya tienes en el carrito todo el stock disponible de este producto.';

                return $this->cartError($request, $message);
            }
        }

        if (isset($carrito[$key])) {
            $carrito[$key]['quantity'] += $requestedQuantity;
        } else {
            $name = $item->name;
            if ($variantLabel) {
                $name .= ' (' . $variantLabel . ')';
            }

            $carrito[$key] = [
                'id'       => $item->id,
                'variant_id' => $variantId,
                'type'     => $request->type,
                'type_label' => $item->type->name ?? 'Catalogo',
                'name'     => $name,
                'price'    => $price,
                'image'    => $item->image,
                'quantity' => $requestedQuantity,
            ];
        }

        session()->put('carrito', $carrito);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Agregado al carrito.']);
        }

        return redirect()->back()->with('success', 'Agregado al carrito.');
    }

    private function cartError(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return redirect()->back()->with('error', $message);
    }

    public function quitar($id)
    {
        $carrito = session()->get('carrito', []);
        unset($carrito[$id]);
        session()->put('carrito', $carrito);

        return redirect()->route('carrito.index')->with('success', 'Ítem eliminado.');
    }

    public function limpiar()
    {
        session()->forget('carrito');
        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }
} 
