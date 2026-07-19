<?php

namespace App\Services\Orders;

use App\Models\CatalogItem;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\Sales\DiscountSaleInventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateSaleFromOrderService
{
    public function __construct(
        private readonly DiscountSaleInventoryService $inventoryService,
    ) {
    }

    public function create(Order $order): ?Sale
    {
        $sale = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()
                ->with(['items.itemable.type', 'items.variant', 'transaction', 'transactions'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->sale_id) {
                return Sale::query()->find($lockedOrder->sale_id);
            }

            if ($lockedOrder->status !== 'paid') {
                return null;
            }

            $payment = $this->resolvePayment($lockedOrder);
            $subtotal = round((float) $lockedOrder->items->sum(fn ($item) => (float) $item->unit_price * (int) $item->quantity), 2);
            $taxTotal = 0.0;
            $discount = 0.0;
            $total = (float) $lockedOrder->total;

            $sale = Sale::create([
                'user_id' => $lockedOrder->user_id,
                'vehicle_id' => $lockedOrder->items->firstWhere('vehicle_id', '!=', null)?->vehicle_id,
                'attended_by' => $lockedOrder->assigned_to,
                'status' => Sale::STATUS_PAID,
                'payment_status' => 'paid',
                'payment_method' => $payment['method'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_total' => $taxTotal,
                'total' => $total,
                'notes' => 'Generada automaticamente desde orden web #' . $lockedOrder->id,
            ]);

            foreach ($lockedOrder->items as $orderItem) {
                $catalogItem = $orderItem->itemable instanceof CatalogItem ? $orderItem->itemable : null;
                $variant = $orderItem->variant;
                $quantity = (int) $orderItem->quantity;
                $unitPrice = (float) $orderItem->unit_price;
                $lineSubtotal = round($unitPrice * $quantity, 2);

                $sale->items()->create([
                    'catalog_item_id' => $catalogItem?->id,
                    'catalog_item_variant_id' => $variant?->id,
                    'vehicle_id' => $orderItem->vehicle_id,
                    'vehicle_type_id' => $orderItem->vehicle_type_id,
                    'name_snapshot' => $orderItem->item_display_name,
                    'type_snapshot' => $orderItem->item_type_label,
                    'description_snapshot' => $catalogItem?->description,
                    'code_snapshot' => $variant?->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => 0,
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineSubtotal,
                ]);
            }

            $sale->payments()->create([
                'method' => $payment['method'],
                'status' => SalePayment::STATUS_APPROVED,
                'amount' => $total,
                'transaction_id' => $payment['transaction_id'],
                'reference' => $payment['reference'],
                'metadata' => $payment['metadata'],
            ]);

            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.created_from_order',
                'payload' => [
                    'order_id' => $lockedOrder->id,
                    'transaction_id' => $payment['transaction_id'],
                    'total' => $total,
                ],
            ]);

            $lockedOrder->update(['sale_id' => $sale->id]);

            return $sale->load(['items', 'payments']);
        });

        if ($sale) {
            $this->discountInventorySafely($sale);
        }

        return $sale;
    }

    private function discountInventorySafely(Sale $sale): void
    {
        try {
            $this->inventoryService->discount($sale);
        } catch (Throwable $exception) {
            Log::error('No se pudo descontar inventario desde orden pagada.', [
                'sale_id' => $sale->id,
                'message' => $exception->getMessage(),
            ]);

            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.inventory_discount_failed',
                'payload' => [
                    'message' => $exception->getMessage(),
                ],
            ]);
        }
    }

    private function resolvePayment(Order $order): array
    {
        $transaction = $order->transactions
            ->where('status', 'approved')
            ->sortByDesc('id')
            ->first()
            ?: $order->transaction;

        $metadata = $transaction?->response_payload ?? [];
        $source = (string) data_get($metadata, 'source', '');
        $method = $transaction?->payphone_ref || str_contains($source, 'payphone')
            ? SalePayment::METHOD_PAYPHONE
            : SalePayment::METHOD_CASH;

        return [
            'method' => $method,
            'transaction_id' => $transaction?->payphone_ref ?: $transaction?->client_transaction_id,
            'reference' => $transaction?->client_transaction_id,
            'metadata' => [
                'order_id' => $order->id,
                'order_type' => $order->order_type,
                'transaction_payload' => $metadata,
            ],
        ];
    }
}
