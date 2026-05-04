<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    private function buildOrderData(float $total, ?int $userId, bool $isReservation = false): array
    {
        $data = [
            'user_id' => $userId,
            'total' => $total,
            'status' => $isReservation ? 'reserved' : 'pending',
        ];

        if (Schema::hasColumn('orders', 'order_type')) {
            $data['order_type'] = $isReservation ? 'reservation' : 'purchase';
        } else {
            // Compatibilidad con esquemas antiguos sin estado "reserved"
            $data['status'] = 'pending';
        }

        return $data;
    }

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
        $order = Order::create($this->buildOrderData((float) $total, auth()->id(), false));

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
        $order->load('items.itemable', 'transaction', 'user');

        if (request()->routeIs('admin.orders.show')) {
            return view('admin.orders.show', compact('order'));
        }

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
        $orders = Order::with('user', 'items')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function reservarServicio(Request $request, Service $service)
    {
        if (!$service->active) {
            return response()->json(['message' => 'Servicio no disponible para reserva.'], 422);
        }

        $source = strtolower(trim(($service->name ?? '') . ' ' . ($service->description ?? '') . ' ' . ($service->category->name ?? '')));
        if (!str_contains($source, 'lavad')) {
            return response()->json(['message' => 'Solo se pueden reservar servicios de lavada.'], 422);
        }

        $order = Order::create($this->buildOrderData((float) $service->price, auth()->id(), true));

        OrderItem::create([
            'order_id'      => $order->id,
            'itemable_type' => Service::class,
            'itemable_id'   => $service->id,
            'quantity'      => 1,
            'unit_price'    => $service->price,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Reserva creada exitosamente.',
            'order_id' => $order->id,
        ]);
    }

    public function marcarPagada(Order $order)
    {
        if ($order->status === 'paid') {
            return redirect()->back()->with('success', 'La orden ya estaba marcada como pagada.');
        }

        $existingApproved = $order->transactions()->where('status', 'approved')->first();
        if (!$existingApproved) {
            Transaction::create([
                'order_id' => $order->id,
                'payphone_ref' => null,
                'amount' => $order->total,
                'status' => 'approved',
                'response_payload' => [
                    'source' => 'manual_admin',
                    'note' => 'Pago confirmado manualmente desde panel admin.',
                ],
                'client_transaction_id' => 'manual-order-' . $order->id . '-' . now()->timestamp,
            ]);
        }

        $order->update(['status' => 'paid']);

        return redirect()->back()->with('success', 'Orden marcada como pagada y transacción registrada.');
    }
}
