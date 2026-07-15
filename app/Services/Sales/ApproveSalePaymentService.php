<?php

namespace App\Services\Sales;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;

class ApproveSalePaymentService
{
    public function __construct(
        private readonly DiscountSaleInventoryService $inventoryService,
    ) {
    }

    public function approve(SalePayment $payment, ?string $transactionId = null, array $metadata = []): Sale
    {
        return DB::transaction(function () use ($payment, $transactionId, $metadata) {
            $payment->update([
                'status' => SalePayment::STATUS_APPROVED,
                'transaction_id' => $transactionId ?: $payment->transaction_id,
                'metadata' => array_filter(array_merge($payment->metadata ?? [], $metadata)),
            ]);

            $sale = $payment->sale()->with(['items.catalogItem', 'items.variant'])->firstOrFail();

            $sale->update([
                'status' => Sale::STATUS_PAID,
                'payment_status' => 'paid',
                'payment_method' => $payment->method,
            ]);

            $this->inventoryService->discount($sale);

            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.payment.approved',
                'payload' => [
                    'payment_id' => $payment->id,
                    'method' => $payment->method,
                    'transaction_id' => $payment->transaction_id,
                ],
            ]);

            return $sale->fresh(['items', 'payments']);
        });
    }
}
