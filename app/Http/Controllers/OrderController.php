<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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
            $data['status'] = 'pending';
        }

        return $data;
    }

    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('carrito.index');
        }

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('checkout.index', compact('carrito', 'total'));
    }

    public function store(Request $request)
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('carrito.index');
        }

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);
        $order = Order::create($this->buildOrderData((float) $total, auth()->id(), false));

        foreach ($carrito as $item) {
            $model = $item['type'] === 'product'
                ? Product::find($item['id'])
                : Service::find($item['id']);

            if (!$model) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'itemable_type' => get_class($model),
                'itemable_id' => $model->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        session()->put('current_order_id', $order->id);

        $payphoneResponse = $this->prepararPagoPayphone($order);

        if (!$payphoneResponse || !isset($payphoneResponse['payWithCard'])) {
            Log::error('Payphone error', ['order_id' => $order->id, 'response' => $payphoneResponse]);
            $order->update(['status' => 'failed']);

            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo conectar con el sistema de pago. Intenta de nuevo.');
        }

        $order->update([
            'payphone_transaction_id' => $payphoneResponse['paymentId'] ?? null,
        ]);

        return redirect()->away($payphoneResponse['payWithCard']);
    }

    private function prepararPagoPayphone(Order $order): ?array
    {
        $montoEnCentavos = (int) round($order->total * 100);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.payphone.token'),
                'Content-Type' => 'application/json',
            ])->post(config('services.payphone.base_url') . '/api/button/Prepare', [
                'amount' => $montoEnCentavos,
                'amountWithTax' => 0,
                'amountWithoutTax' => $montoEnCentavos,
                'tax' => 0,
                'clientTransactionId' => 'order-' . $order->id . '-' . time(),
                'storeId' => config('services.payphone.store_id'),
                'responseUrl' => route('payphone.success'),
                'cancellationUrl' => route('payphone.cancel'),
                'currency' => 'USD',
                'reference' => 'Orden #' . $order->id . ' - ' . config('app.name'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Payphone prepare failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'order_id' => $order->id,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Payphone exception', ['message' => $e->getMessage()]);

            return null;
        }
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.itemable', 'transaction', 'user');

        if (request()->routeIs('admin.orders.show')) {
            return view('admin.orders.show', compact('order'));
        }

        return view('checkout.show', compact('order'));
    }

    public function confirmacion(Order $order)
    {
        $order->load('items.itemable', 'transaction');

        return view('checkout.confirmacion', compact('order'));
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $orders = Order::with('user', 'items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('id', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");

                    if (Schema::hasColumn('orders', 'order_type')) {
                        $subQuery->orWhere('order_type', 'like', "%{$search}%");
                    }

                    $subQuery->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
            'order_id' => $order->id,
            'itemable_type' => Service::class,
            'itemable_id' => $service->id,
            'quantity' => 1,
            'unit_price' => $service->price,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Reserva creada exitosamente.',
            'order_id' => $order->id,
        ]);
    }

    public function reservarCatalogo(Request $request)
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer'],
            'item_type' => ['required', Rule::in(['product', 'service'])],
        ]);

        $model = null;

        if ($data['item_type'] === 'product') {
            $model = Product::query()->where('active', true)->find($data['item_id']);
        }

        if ($data['item_type'] === 'service') {
            $model = Service::query()->with('category')->where('active', true)->find($data['item_id']);
        }

        if (!$model) {
            return response()->json(['message' => 'El item seleccionado no esta disponible.'], 404);
        }

        if ($model instanceof Service) {
            $source = strtolower(trim(($model->name ?? '') . ' ' . ($model->description ?? '') . ' ' . ($model->category->name ?? '')));

            if (!str_contains($source, 'lavad')) {
                return response()->json(['message' => 'Solo se pueden reservar servicios de lavada.'], 422);
            }
        }

        $reservationPrice = $model instanceof Product ? (float) $model->display_price : (float) $model->price;
        $order = Order::create($this->buildOrderData($reservationPrice, auth()->id(), true));

        OrderItem::create([
            'order_id' => $order->id,
            'itemable_type' => get_class($model),
            'itemable_id' => $model->id,
            'quantity' => 1,
            'unit_price' => $reservationPrice,
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

        return redirect()->back()->with('success', 'Orden marcada como pagada y transaccion registrada.');
    }

    public function destroy(Order $order)
    {
        $order->load('transactions');

        foreach ($order->transactions as $transaction) {
            $transaction->delete();
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Orden eliminada correctamente.');
    }
}
