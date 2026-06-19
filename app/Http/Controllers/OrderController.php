<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Services\ServiceVehiclePriceResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private function createOrderFromCart(array $carrito, ServiceVehiclePriceResolver $priceResolver): Order
    {
        $resolvedItems = $this->resolveCartItems($carrito, $priceResolver);
        $total = collect($resolvedItems)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $order = Order::create($this->buildOrderData((float) $total, auth()->id(), false));

        foreach ($resolvedItems as $item) {
            $model = $item['model'];
            OrderItem::create([
                'order_id' => $order->id,
                'itemable_type' => get_class($model),
                'itemable_id' => $model->id,
                'vehicle_id' => $item['vehicle_id'],
                'vehicle_type_id' => $item['vehicle_type_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ]);
        }

        return $order;
    }

    private function buildPayphoneAmounts(int $totalCents): array
    {
        $taxPercent = (int) config('services.payphone.tax', 0);
        $amountWithTax = 0;      // Base imponible
        $amountWithoutTax = $totalCents; // Base no imponible
        $taxAmount = 0;          // Valor de impuesto en centavos

        if ($taxPercent > 0) {
            // Asumimos que el total incluye impuesto sobre toda la base imponible.
            $amountWithTax = (int) round($totalCents / (1 + ($taxPercent / 100)));
            $taxAmount = max(0, $totalCents - $amountWithTax);
            $amountWithoutTax = 0;
        }

        return [
            'tax_percent' => $taxPercent,
            'amount' => $totalCents,
            'amount_with_tax' => $amountWithTax,
            'amount_without_tax' => $amountWithoutTax,
            'tax' => $taxAmount,
        ];
    }

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

    public function checkout(Request $request)
    {
        $carrito = (array) $request->session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('carrito.index');
        }

        $total = collect($carrito)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('checkout.index', compact('carrito', 'total'));
    }

    public function store(Request $request, ServiceVehiclePriceResolver $priceResolver)
    {
        $carrito = (array) $request->session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('carrito.index');
        }

        $order = $this->createOrderFromCart($carrito, $priceResolver);

        $request->session()->put('current_order_id', $order->id);

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

        Transaction::create([
            'order_id' => $order->id,
            'payphone_ref' => $payphoneResponse['paymentId'] ?? null,
            'amount' => $order->total,
            'status' => 'pending',
            'response_payload' => $payphoneResponse,
            'client_transaction_id' => $payphoneResponse['clientTransactionId'] ?? null,
        ]);

        return redirect()->away($payphoneResponse['payWithCard']);
    }

    public function prepareBox(Request $request, ServiceVehiclePriceResolver $priceResolver)
    {
        $carrito = (array) $request->session()->get('carrito', []);

        if (empty($carrito)) {
            return response()->json([
                'ok' => false,
                'message' => 'El carrito esta vacio.',
            ], 422);
        }

        $order = $this->createOrderFromCart($carrito, $priceResolver);
        $totalCents = (int) round($order->total * 100);
        $amounts = $this->buildPayphoneAmounts($totalCents);
        $clientTransactionId = 'order-' . $order->id . '-' . Str::uuid()->toString();

        $request->session()->put('current_order_id', $order->id);

        Transaction::create([
            'order_id' => $order->id,
            'payphone_ref' => null,
            'amount' => $order->total,
            'status' => 'pending',
            'response_payload' => [
                'source' => 'payphone_box',
                'created_at' => now()->toISOString(),
            ],
            'client_transaction_id' => $clientTransactionId,
        ]);

        return response()->json([
            'ok' => true,
            'token' => config('services.payphone.box_token'),
            'storeId' => config('services.payphone.store_id'),
            'clientTransactionId' => $clientTransactionId,
            'amount' => $amounts['amount'],
            'amountWithoutTax' => $amounts['amount_without_tax'],
            'amountWithTax' => $amounts['amount_with_tax'],
            'tax' => $amounts['tax'],
            'currency' => (string) config('services.payphone.currency', 'USD'),
            'reference' => 'Orden #' . $order->id . ' - ' . config('app.name'),
            'timeZone' => (int) config('services.payphone.timezone', -5),
        ]);
    }

    private function prepararPagoPayphone(Order $order): ?array
    {
        $currency = (string) config('services.payphone.currency', 'USD');
        $taxPercent = (int) config('services.payphone.tax', 0);
        $montoEnCentavos = (int) round($order->total * 100);
        $clientTransactionId = 'order-' . $order->id . '-' . Str::uuid()->toString();

        $amountWithTax = 0;
        $amountWithoutTax = $montoEnCentavos;

        if ($taxPercent > 0) {
            $amountWithoutTax = (int) round($montoEnCentavos / (1 + ($taxPercent / 100)));
            $amountWithTax = max(0, $montoEnCentavos - $amountWithoutTax);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.payphone.token'),
                'Content-Type' => 'application/json',
            ])->post(config('services.payphone.base_url') . '/api/button/Prepare', [
                'amount' => $montoEnCentavos,
                'amountWithTax' => $amountWithTax,
                'amountWithoutTax' => $amountWithoutTax,
                'tax' => $taxPercent,
                'clientTransactionId' => $clientTransactionId,
                'storeId' => config('services.payphone.store_id'),
                'responseUrl' => route('transaccion.exitosa'),
                'cancellationUrl' => route('payphone.cancel'),
                'currency' => $currency,
                'reference' => 'Orden #' . $order->id . ' - ' . config('app.name'),
            ]);

            if ($response->successful()) {
                $payload = $response->json();
                $payload['clientTransactionId'] = $clientTransactionId;
                return $payload;
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
        $order->load('items.itemable', 'items.vehicle.brand', 'items.vehicle.model', 'items.vehicleType', 'transaction', 'user');

        if (request()->routeIs('admin.orders.show')) {
            return view('admin.orders.show', compact('order'));
        }

        return redirect()->route('orden.confirmacion', $order);
    }

    public function confirmacion(Order $order)
    {
        $order->load('items.itemable', 'items.vehicle.brand', 'items.vehicle.model', 'items.vehicleType', 'transaction');

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

    public function reservarCatalogo(Request $request, ServiceVehiclePriceResolver $priceResolver)
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer'],
            'item_type' => ['required', Rule::in(['catalog'])],
            'vehicle_id' => ['nullable', 'integer'],
            'vehicle_type_id' => ['nullable', 'integer'],
        ]);

        /** @var CatalogItem|null $model */
        $model = CatalogItem::query()
            ->with(['type', 'category', 'activeVariants'])
            ->where('active', true)
            ->where('reservable', true)
            ->find($data['item_id']);

        if (!$model) {
            return response()->json(['message' => 'El item seleccionado no esta disponible.'], 404);
        }

        $vehicleContext = $priceResolver->resolve(
            $model,
            $request->integer('vehicle_id') ?: null,
            $request->integer('vehicle_type_id') ?: null,
            auth()->id()
        );
        $reservationPrice = $vehicleContext['price'];
        $order = Order::create($this->buildOrderData($reservationPrice, auth()->id(), true));

        OrderItem::create([
            'order_id' => $order->id,
            'itemable_type' => get_class($model),
            'itemable_id' => $model->id,
            'vehicle_id' => $vehicleContext['vehicle_id'],
            'vehicle_type_id' => $vehicleContext['vehicle_type_id'],
            'quantity' => 1,
            'unit_price' => $reservationPrice,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Reserva creada exitosamente.',
            'order_id' => $order->id,
        ]);
    }

    private function resolveCartItems(array $cart, ServiceVehiclePriceResolver $priceResolver): array
    {
        $resolved = [];

        foreach ($cart as $item) {
            if ((string) data_get($item, 'type') !== 'catalog') {
                continue;
            }

            $model = CatalogItem::query()
                ->with(['type', 'vehicleTypePrices.vehicleType'])
                ->where('active', true)
                ->where('purchasable', true)
                ->find((int) data_get($item, 'id'));

            if (!$model) {
                continue;
            }

            $vehicleContext = $priceResolver->resolve(
                $model,
                data_get($item, 'vehicle_id') ? (int) data_get($item, 'vehicle_id') : null,
                data_get($item, 'vehicle_type_id') ? (int) data_get($item, 'vehicle_type_id') : null,
                auth()->id()
            );

            $isService = ($model->type?->business_model ?? \App\Models\CatalogType::BUSINESS_MODEL_SERVICES)
                === \App\Models\CatalogType::BUSINESS_MODEL_SERVICES;

            $resolved[] = [
                'model' => $model,
                'quantity' => max(1, (int) data_get($item, 'quantity', 1)),
                'price' => $isService ? $vehicleContext['price'] : (float) data_get($item, 'price', $model->display_price),
                'vehicle_id' => $vehicleContext['vehicle_id'],
                'vehicle_type_id' => $vehicleContext['vehicle_type_id'],
            ];
        }

        return $resolved;
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
        Transaction::query()
            ->where('order_id', $order->id)
            ->delete();

        Order::query()
            ->whereKey($order->id)
            ->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Orden eliminada correctamente.');
    }
}
