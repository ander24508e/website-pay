<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        // Crear items usando precio real en BD (no desde sesiÃ³n)
        $lineItems = [];
        foreach ($carrito as $item) {
            $model = $item['type'] === 'product'
                ? Product::where('active', true)->find($item['id'])
                : Service::where('active', true)->find($item['id']);

            if (!$model) continue;

            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            $lineItems[] = [
                'itemable_type' => get_class($model),
                'itemable_id'   => $model->id,
                'quantity'      => $quantity,
                'unit_price'    => $model->price,
            ];
        }

        if (empty($lineItems)) {
            return redirect()->route('carrito.index')
                ->with('error', 'No hay Ã­tems vÃ¡lidos para procesar el pago.');
        }

        $total = collect($lineItems)->sum(fn($row) => $row['unit_price'] * $row['quantity']);

        // Crear la orden
        $order = Order::create([
            'user_id' => auth()->id(),
            'total'   => $total,
            'status'  => 'pending',
        ]);

        foreach ($lineItems as $row) {
            OrderItem::create([
                'order_id'      => $order->id,
                'itemable_type' => $row['itemable_type'],
                'itemable_id'   => $row['itemable_id'],
                'quantity'      => $row['quantity'],
                'unit_price'    => $row['unit_price'],
            ]);
        }

        session()->put('current_order_id', $order->id);

        // Llamar a Payphone
        $payphoneResponse = $this->iniciarPagoPayphone($order);

        if (!$payphoneResponse || isset($payphoneResponse['error'])) {
            $order->update(['status' => 'failed']);
            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo iniciar el pago. Intenta de nuevo.');
        }

        // Guardar el paymentId de Payphone en la orden
        $order->update([
            'payphone_transaction_id' => $payphoneResponse['paymentId'] ?? null,
        ]);

        // Redirigir al link de pago de Payphone
        return redirect()->away($payphoneResponse['payWithCard']);
    }

    private function iniciarPagoPayphone(Order $order): ?array
    {
        $montoEnCentavos = (int) round($order->total * 100);

        $response = Http::withToken(config('services.payphone.token'))
            ->post(config('services.payphone.base_url') . '/api/button/Prepare', [
                'amount'          => $montoEnCentavos,
                'amountWithTax'   => 0,
                'amountWithoutTax'=> $montoEnCentavos,
                'tax'             => 0,
                'clientTransactionId' => 'order-' . $order->id . '-' . time(),
                'storeId'         => config('services.payphone.store_id'),
                'responseUrl'     => route('payphone.success'),
                'cancellationUrl' => route('payphone.cancel'),
                'currency'        => 'USD',
                'reference'       => 'Orden #' . $order->id,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.itemable', 'transaction');
        return view('checkout.show', compact('order'));
    }

    public function confirmacion(Order $order)
    {
        $this->authorize('view', $order);
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
