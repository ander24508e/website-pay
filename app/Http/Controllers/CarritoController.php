<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
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
            'type'     => 'required|in:product,service',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $request->type === 'product'
            ? Product::where('active', true)->findOrFail($request->id)
            : Service::where('active', true)->findOrFail($request->id);

        $carrito = session()->get('carrito', []);
        $key = $request->type . '_' . $request->id;

        if (isset($carrito[$key])) {
            $carrito[$key]['quantity'] += $request->quantity;
        } else {
            $price = $request->type === 'product' ? $item->display_price : $item->price;
            $carrito[$key] = [
                'id'       => $item->id,
                'type'     => $request->type,
                'name'     => $item->name,
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
