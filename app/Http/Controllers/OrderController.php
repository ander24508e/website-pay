<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // ══════════════════════════════════════════
    // PÚBLICO — Vista checkout (resumen)
    // ══════════════════════════════════════════
    public function checkout()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('carrito.index');

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('checkout.index', compact('carrito', 'total'));
    }

    // ══════════════════════════════════════════
    // PÚBLICO — Crear orden y redirigir a Payphone
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->route('carrito.index');

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);

        // 1. Crear la orden en BD
        $order = Order::create([
            'user_id' => auth()->id(),
            'total'   => $total,
            'status'  => 'pending',
        ]);

        // 2. Crear los items de la orden
        foreach ($carrito as $item) {
            $model = $item['type'] === 'product'
                ? Product::find($item['id'])
                : Service::find($item['id']);

            if (!$model) continue;

            OrderItem::create([
                'order_id'      => $order->id,
                'itemable_type' => get_class($model),
                'itemable_id'   => $model->id,
                'quantity'      => $item['quantity'],
                'unit_price'    => $item['price'],
            ]);
        }

        // 3. Guardar orden en sesión para el callback
        session()->put('current_order_id', $order->id);

        // 4. Preparar pago con Payphone
        $payphoneResponse = $this->prepararPagoPayphone($order);

        // 5. Si falla Payphone — cancelar orden y volver al carrito
        if (!$payphoneResponse || !isset($payphoneResponse['payWithCard'])) {
            Log::error('Payphone error', ['order_id' => $order->id, 'response' => $payphoneResponse]);
            $order->update(['status' => 'failed']);
            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo conectar con el sistema de pago. Intenta de nuevo.');
        }

        // 6. Guardar el paymentId de Payphone
        $order->update([
            'payphone_transaction_id' => $payphoneResponse['paymentId'] ?? null,
        ]);

        // 7. Redirigir al link de pago de Payphone
        return redirect()->away($payphoneResponse['payWithCard']);
    }

    // ══════════════════════════════════════════
    // PRIVADO — Llamada a Payphone API
    // ══════════════════════════════════════════
    private function prepararPagoPayphone(Order $order): ?array
    {
        // Payphone trabaja en CENTAVOS — multiplicamos por 100
        $montoEnCentavos = (int) round($order->total * 100);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.payphone.token'),
                'Content-Type'  => 'application/json',
            ])->post(config('services.payphone.base_url') . '/api/button/Prepare', [
                'amount'              => $montoEnCentavos,
                'amountWithTax'       => 0,
                'amountWithoutTax'    => $montoEnCentavos,
                'tax'                 => 0,
                'clientTransactionId' => 'order-' . $order->id . '-' . time(),
                'storeId'             => config('services.payphone.store_id'),
                'responseUrl'         => route('payphone.success'),
                'cancellationUrl'     => route('payphone.cancel'),
                'currency'            => 'USD',
                'reference'           => 'Orden #' . $order->id . ' — ' . config('app.name'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Payphone prepare failed', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'order_id' => $order->id,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Payphone exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ══════════════════════════════════════════
    // CLIENTE — Ver detalle de su orden
    // ══════════════════════════════════════════
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.itemable', 'transaction');
        return view('checkout.show', compact('order'));
    }

    // ══════════════════════════════════════════
    // CLIENTE — Página de confirmación exitosa
    // ══════════════════════════════════════════
    public function confirmacion(Order $order)
    {
        $order->load('items.itemable', 'transaction');
        return view('checkout.confirmacion', compact('order'));
    }

    // ══════════════════════════════════════════
    // ADMIN — Listado de todas las órdenes
    // ══════════════════════════════════════════
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }
}