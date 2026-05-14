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
            $variant = $item->activeVariants()->orderByDesc('is_default')->orderBy('sort_order')->orderBy('price')->first();
        }

        if ($variant) {
            $variantId = $variant->id;
            $variantLabel = trim(($variant->presentation ?? '') . ' ' . ($variant->specification ?? ''));
            $price = (float) ($variant->price ?? $item->display_price);
        }

        $key = $request->type . '_' . $request->id . ($variantId ? ('_v' . $variantId) : '');

        if (isset($carrito[$key])) {
            $carrito[$key]['quantity'] += $request->quantity;
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
                'quantity' => $request->quantity,
            ];
        }

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', 'Agregado al carrito.');
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
