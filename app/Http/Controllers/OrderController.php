<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderItem;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('carrito.index');

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('checkout.index', compact('carrito', 'total'));
    }

    public function store(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('carrito.index');

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);

        $order = Order::create([
            'user_id' => auth()->id(),
            'total'   => $total,
            'status'  => 'pending',
        ]);

        foreach ($carrito as $item) {
            $model = $item['type'] === 'product'
                ? Product::find($item['id'])
                : Service::find($item['id']);

            OrderItems::create([
                'order_id'      => $order->id,
                'itemable_type' => get_class($model),
                'itemable_id'   => $model->id,
                'quantity'      => $item['quantity'],
                'unit_price'    => $item['price'],
            ]);
        }

        session()->put('current_order_id', $order->id);

        return redirect()->route('orden.show', $order);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.itemable', 'transaction');
        return view('checkout.show', compact('order'));
    }

    public function confirmacion(Order $order)
    {
        $order->load('items.itemable', 'transaction');
        return view('checkout.confirmacion', compact('order'));
    }

    // Admin
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }
}