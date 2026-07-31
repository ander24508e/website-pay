<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Services\ServiceVehiclePriceResolver;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = session()->get('carrito', []);
        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('carrito.index', compact('carrito', 'total'));
    }

    public function agregar(Request $request, ServiceVehiclePriceResolver $priceResolver)
    {
        $request->validate([
            'id'       => 'required|integer',
            'type'     => 'required|in:catalog',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer',
            'vehicle_id' => 'nullable|integer',
            'vehicle_type_id' => 'nullable|integer',
        ]);

        $item = CatalogItem::with(['type', 'vehicleTypePrices.vehicleType', 'activeVariants'])
            ->where('active', true)
            ->where('purchasable', true)
            ->findOrFail($request->id);

        $carrito = session()->get('carrito', []);
        $key = $request->type . '_' . $request->id;

        $variantId = null;
        $variantLabel = null;
        $price = (float) $item->display_price;
        $vehicleContext = [
            'vehicle_id' => null,
            'vehicle_type_id' => null,
            'vehicle_label' => null,
            'vehicle_type_label' => null,
        ];

        $isService = ($item->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_SERVICES;
        $isProduct = !$isService;

        if ($isService) {
            $vehicleContext = $priceResolver->resolve(
                $item,
                $request->integer('vehicle_id') ?: null,
                $request->integer('vehicle_type_id') ?: null,
                auth()->id()
            );
            $price = $vehicleContext['price'];
        }

        if ($isProduct && $request->filled('variant_id')) {
            $variant = CatalogItemVariant::query()
                ->where('catalog_item_id', $item->id)
                ->where('active', true)
                ->find($request->variant_id);
        } elseif ($isProduct) {
            $variantQuery = $item->activeVariants()
                ->when($item->uses_inventory, fn ($query) => $query->where('stock', '>', 0))
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->orderBy('price');
            $variant = $variantQuery->first();
        }

        if ($isProduct && !$variant) {
            return $this->cartError($request, 'Este producto no tiene una presentacion activa disponible.');
        }

        if ($variant) {
            $variantId = $variant->id;
            $variantLabel = trim((string) ($variant->name ?? ''));
            $price = (float) ($variant->price ?? 0);
        }

        $key = $request->type . '_' . $request->id . ($variantId ? ('_v' . $variantId) : '');
        if ($vehicleContext['vehicle_id']) {
            $key .= '_vehicle' . $vehicleContext['vehicle_id'];
        } elseif ($vehicleContext['vehicle_type_id']) {
            $key .= '_type' . $vehicleContext['vehicle_type_id'];
        }
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
                'vehicle_id' => $vehicleContext['vehicle_id'],
                'vehicle_type_id' => $vehicleContext['vehicle_type_id'],
                'vehicle_label' => $vehicleContext['vehicle_label'],
                'vehicle_type_label' => $vehicleContext['vehicle_type_label'],
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

    public function actualizar(Request $request, string $id)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$id])) {
            return redirect()->route('carrito.index')->with('error', 'El ítem no existe en el carrito.');
        }

        $quantity = (int) $data['quantity'];
        $cartItem = $carrito[$id];
        $catalogItem = CatalogItem::query()
            ->with('type')
            ->where('active', true)
            ->where('purchasable', true)
            ->find($cartItem['id'] ?? null);

        if (!$catalogItem) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);

            return redirect()->route('carrito.index')
                ->with('error', 'Uno de los items ya no esta disponible y fue retirado del carrito.');
        }

        if ($catalogItem?->uses_inventory) {
            $variant = CatalogItemVariant::query()
                ->where('catalog_item_id', $catalogItem->id)
                ->where('active', true)
                ->find($cartItem['variant_id'] ?? null);

            if (!$variant) {
                return redirect()->route('carrito.index')
                    ->with('error', 'Este producto no tiene una presentación inventariable disponible.');
            }

            $availableStock = max(0, (int) ($variant->stock ?? 0));

            if ($quantity > $availableStock) {
                return redirect()->route('carrito.index')
                    ->with('error', "Solo hay {$availableStock} unidad(es) disponibles de este producto.");
            }
        }

        $carrito[$id]['quantity'] = $quantity;
        session()->put('carrito', $carrito);

        return redirect()->route('carrito.index')->with('success', 'Cantidad actualizada.');
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
