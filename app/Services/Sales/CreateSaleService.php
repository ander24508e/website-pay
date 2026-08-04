<?php

namespace App\Services\Sales;

use App\Data\SaleData;
use App\Models\CatalogItem;
use App\Models\CatalogItemVariant;
use App\Models\CatalogType;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\ServiceVehiclePriceResolver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleService
{
    public function __construct(
        private readonly ServiceVehiclePriceResolver $priceResolver,
        private readonly DiscountSaleInventoryService $inventoryService,
    ) {
    }

    public function create(SaleData $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $resolvedItems = $this->resolveItems($data);
            $subtotal = round((float) collect($resolvedItems)->sum('subtotal'), 2);
            $discount = 0.0;
            $taxTotal = round((float) collect($resolvedItems)->sum('tax_amount'), 2);
            $total = round($subtotal - $discount + $taxTotal, 2);
            $payment = $this->resolvePayment($data->payment, $total);
            $saleStatus = $payment['sale_status'];

            $sale = Sale::create([
                'user_id' => $data->userId,
                'vehicle_id' => $data->vehicleId,
                'attended_by' => $data->attendedBy ?: auth()->id(),
                'status' => $saleStatus,
                'payment_status' => $payment['legacy_payment_status'],
                'payment_method' => $payment['method'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax_total' => $taxTotal,
                'total' => $total,
                'notes' => $data->notes,
            ]);

            $sale->payments()->create($payment['payload']);

            foreach ($resolvedItems as $item) {
                $sale->items()->create($item['payload']);
            }

            if ($saleStatus === 'paid') {
                $this->inventoryService->discount($sale);
            }

            $sale->audits()->create([
                'user_id' => auth()->id(),
                'action' => 'sale.created',
                'payload' => [
                    'payment_method' => $payment['method'],
                    'payment_status' => $payment['payload']['status'],
                    'total' => $total,
                ],
            ]);

            return $sale->load(['items', 'payments']);
        });
    }

    private function resolveItems(SaleData $data): array
    {
        $resolved = [];

        foreach ($data->items as $index => $row) {
            $catalogItem = CatalogItem::query()
                ->with([
                    'type',
                    'activeVariants',
                    'vehicleTypePrices.vehicleType',
                    'vehicleTypePrices.vehicleSpecification.brand',
                    'vehicleTypePrices.vehicleSpecification.model',
                    'vehicleTypePrices.vehicleSpecification.type',
                ])
                ->where('active', true)
                ->where('purchasable', true)
                ->find((int) ($row['catalog_item_id'] ?? 0));

            if (!$catalogItem) {
                throw ValidationException::withMessages(["items.{$index}.catalog_item_id" => 'El item seleccionado no esta disponible.']);
            }

            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $isProduct = ($catalogItem->type?->business_model ?? CatalogType::BUSINESS_MODEL_SERVICES) === CatalogType::BUSINESS_MODEL_PRODUCTS;
            $variant = null;
            $unitPrice = (float) $catalogItem->display_price;

            if ($isProduct) {
                $variant = $this->resolveVariant($catalogItem, $row['catalog_item_variant_id'] ?? null);
                if (!$variant) {
                    throw ValidationException::withMessages(["items.{$index}.catalog_item_variant_id" => 'Selecciona una presentacion activa para este producto.']);
                }

                $unitPrice = (float) ($variant->price ?? 0);

                if ($catalogItem->uses_inventory && (int) $variant->stock < $quantity) {
                    throw ValidationException::withMessages(["items.{$index}.quantity" => 'No hay stock suficiente para este producto.']);
                }
            } else {
                $vehicleContext = $this->priceResolver->resolve(
                    $catalogItem,
                    !empty($row['vehicle_id']) ? (int) $row['vehicle_id'] : null,
                    !empty($row['vehicle_specification_id']) ? (int) $row['vehicle_specification_id'] : null,
                    $data->userId,
                    !empty($row['vehicle_type_id']) ? (int) $row['vehicle_type_id'] : null
                );
                $unitPrice = (float) $vehicleContext['price'];
            }

            $subtotal = round($unitPrice * $quantity, 2);
            $taxRate = 0.0;
            $taxAmount = 0.0;
            $discountAmount = 0.0;
            $total = round($subtotal - $discountAmount + $taxAmount, 2);

            $resolved[] = [
                'variant' => $variant,
                'uses_inventory' => (bool) $catalogItem->uses_inventory,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'payload' => [
                    'catalog_item_id' => $catalogItem->id,
                    'catalog_item_variant_id' => $isProduct ? $variant->id : null,
                    'vehicle_id' => !$isProduct && !empty($row['vehicle_id']) ? (int) $row['vehicle_id'] : null,
                    'vehicle_type_id' => !$isProduct ? ($vehicleContext['vehicle_type_id'] ?? null) : null,
                    'name_snapshot' => $catalogItem->name,
                    'type_snapshot' => $catalogItem->type?->name,
                    'description_snapshot' => $catalogItem->description,
                    'code_snapshot' => $variant?->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotal,
                    'total' => $total,
                ],
            ];
        }

        return $resolved;
    }

    private function resolvePayment(array $payment, float $total): array
    {
        $method = $payment['method'] ?? SalePayment::METHOD_CASH;
        $status = in_array($method, [SalePayment::METHOD_CASH, SalePayment::METHOD_TRANSFER, SalePayment::METHOD_CARD], true)
            ? SalePayment::STATUS_APPROVED
            : SalePayment::STATUS_PENDING;

        $receivedAmount = isset($payment['received_amount']) ? (float) $payment['received_amount'] : null;
        $changeAmount = $method === SalePayment::METHOD_CASH && $receivedAmount !== null
            ? max(0, round($receivedAmount - $total, 2))
            : null;

        if ($method === SalePayment::METHOD_CASH && $receivedAmount !== null && $receivedAmount < $total) {
            throw ValidationException::withMessages(['payment.received_amount' => 'El monto recibido no cubre el total de la venta.']);
        }

        return [
            'method' => $method,
            'sale_status' => $status === SalePayment::STATUS_APPROVED ? Sale::STATUS_PAID : Sale::STATUS_PENDING,
            'legacy_payment_status' => $status === SalePayment::STATUS_APPROVED ? 'paid' : 'pending',
            'payload' => [
                'method' => $method,
                'status' => $status,
                'amount' => $total,
                'received_amount' => $receivedAmount,
                'change_amount' => $changeAmount,
                'transaction_id' => $payment['transaction_id'] ?? null,
                'bank' => $payment['bank'] ?? null,
                'reference' => $payment['reference'] ?? null,
                'proof_path' => $this->storeProof($payment['proof'] ?? null),
                'authorization_code' => $payment['authorization_code'] ?? null,
                'due_date' => $payment['due_date'] ?? null,
                'notes' => trim((string) ($payment['notes'] ?? '')) ?: null,
            ],
        ];
    }

    private function storeProof(mixed $proof): ?string
    {
        if (!$proof instanceof UploadedFile) {
            return null;
        }

        return $proof->store('sale-payment-proofs', 'public');
    }

    private function resolveVariant(CatalogItem $catalogItem, mixed $variantId): ?CatalogItemVariant
    {
        if ($variantId) {
            $variant = CatalogItemVariant::query()
                ->where('catalog_item_id', $catalogItem->id)
                ->where('active', true)
                ->find((int) $variantId);

            if (!$variant) {
                throw ValidationException::withMessages([
                    'items' => 'La presentación seleccionada no pertenece al item o no está activa.',
                ]);
            }

            return $variant;
        }

        return $catalogItem->activeVariants
            ->sortByDesc('is_default')
            ->first();
    }

}
