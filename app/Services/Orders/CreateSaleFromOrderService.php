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

            if ($lockedOrder->status !== 'paid') {
                return null;
            }

            $payment = $this->resolvePayment($lockedOrder);

            if ($lockedOrder->sale_id) {
                return $this->finalizeLinkedSale($lockedOrder, $payment);
            }

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
                'received_amount' => $payment['received_amount'],
                'change_amount' => $payment['change_amount'],
                'transaction_id' => $payment['transaction_id'],
                'bank' => $payment['bank'],
                'reference' => $payment['reference'],
                'proof_path' => $payment['proof_path'],
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

    private function finalizeLinkedSale(Order $order, array $payment): Sale
    {
        $sale = Sale::query()->lockForUpdate()->findOrFail($order->sale_id);
        $wasPaid = $sale->status === Sale::STATUS_PAID && $sale->payment_status === 'paid';

        $sale->update([
            'status' => Sale::STATUS_PAID,
            'payment_status' => 'paid',
            'payment_method' => $payment['method'],
        ]);

        $existingPayment = $sale->payments()
            ->where('status', SalePayment::STATUS_APPROVED)
            ->when(
                $payment['transaction_id'],
                fn ($query) => $query->where('transaction_id', $payment['transaction_id'])
            )
            ->first();

        if ($existingPayment) {
            $existingPayment->update(array_filter([
                'bank' => $payment['bank'],
                'reference' => $payment['reference'],
                'proof_path' => $payment['proof_path'],
            ], fn ($value) => $value !== null && $value !== ''));
        } else {
            $sale->payments()->create([
                'method' => $payment['method'],
                'status' => SalePayment::STATUS_APPROVED,
                'amount' => $sale->total,
                'received_amount' => $payment['received_amount'],
                'change_amount' => $payment['change_amount'],
                'transaction_id' => $payment['transaction_id'],
                'bank' => $payment['bank'],
                'reference' => $payment['reference'],
                'proof_path' => $payment['proof_path'],
                'metadata' => $payment['metadata'],
            ]);
        }

        if (!$wasPaid) {
            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.paid_from_order',
                'payload' => [
                    'order_id' => $order->id,
                    'transaction_id' => $payment['transaction_id'],
                    'total' => (float) $sale->total,
                ],
            ]);
        }

        return $sale->load(['items', 'payments']);
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
        $allowedMethods = [
            SalePayment::METHOD_CASH,
            SalePayment::METHOD_TRANSFER,
            SalePayment::METHOD_CARD,
            SalePayment::METHOD_PAYPHONE,
        ];
        $metadataMethod = (string) data_get($metadata, 'payment_method', '');
        $method = in_array($metadataMethod, $allowedMethods, true)
            ? $metadataMethod
            : ($transaction?->payphone_ref || str_contains($source, 'payphone')
                ? SalePayment::METHOD_PAYPHONE
                : SalePayment::METHOD_CASH);
        $receivedAmount = round((float) data_get($metadata, 'received_amount', $order->total), 2);
        $changeAmount = round((float) data_get($metadata, 'change_amount', max(0, $receivedAmount - (float) $order->total)), 2);

        return [
            'method' => $method,
            'received_amount' => $receivedAmount,
            'change_amount' => $changeAmount,
            'transaction_id' => $transaction?->payphone_ref ?: $transaction?->client_transaction_id,
            'bank' => data_get($metadata, 'bank'),
            'reference' => data_get($metadata, 'reference') ?: $transaction?->client_transaction_id,
            'proof_path' => data_get($metadata, 'proof_path'),
            'metadata' => [
                'order_id' => $order->id,
                'order_type' => $order->order_type,
                'transaction_payload' => $metadata,
            ],
        ];
    }
}
