<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Order;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class CheckoutReceiptService
{
    public function build(Order $order): array
    {
        $order->loadMissing(['items.itemable', 'transaction', 'user']);

        $empresa = Empresa::query()->first();
        $verificationUrl = route('orden.confirmacion', $order);
        $orderCode = 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $transactionCode = $order->transaction?->payphone_ref
            ?: 'TX-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $itemsSummary = $order->items
            ->map(fn ($item) => ($item->itemable->name ?? $item->item_display_name) . ' × ' . $item->quantity)
            ->values();

        $qrPayload = [
            'business' => $empresa->nombre ?? 'Mi negocio',
            'client' => $order->user->name ?? 'Invitado',
            'items' => $itemsSummary->implode(', '),
            'order' => $orderCode,
            'total' => number_format((float) $order->total, 2, '.', ''),
            'transaction' => $transactionCode,
            'verify' => $verificationUrl,
        ];

        $qrResult = (new Builder(
            writer: new SvgWriter(),
            writerOptions: [],
            validateResult: false,
            data: json_encode($qrPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 240,
            margin: 12,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        $whatsappMessage = implode("\n", [
            'Hola, adjunto la captura de mi comprobante de pago.',
            'Orden: ' . $orderCode,
            'Total: $' . number_format((float) $order->total, 2),
            'Transaccion: ' . $transactionCode,
            'Verificacion QR: ' . $verificationUrl,
        ]);

        $whatsappUrl = $this->buildWhatsappUrl($empresa?->whatsapp_url, $whatsappMessage);

        return [
            'empresa' => $empresa,
            'primario' => $empresa?->color_primario_hex ?? '#D82128',
            'secundario' => $empresa?->color_secundario_hex ?? '#F0B429',
            'terciario' => $empresa?->color_terciario_hex ?? '#94a3b8',
            'verificationUrl' => $verificationUrl,
            'orderCode' => $orderCode,
            'transactionCode' => $transactionCode,
            'itemsSummary' => $itemsSummary,
            'qrCodeDataUri' => $qrResult->getDataUri(),
            'whatsappUrl' => $whatsappUrl,
        ];
    }

    private function buildWhatsappUrl(?string $empresaWhatsappUrl, string $message): ?string
    {
        if (!$empresaWhatsappUrl) {
            return null;
        }

        $baseWhatsappUrl = preg_replace('/([?&])text=[^&]*/', '$1', $empresaWhatsappUrl);
        $baseWhatsappUrl = rtrim((string) $baseWhatsappUrl, '?&');
        $separator = str_contains($baseWhatsappUrl, '?') ? '&' : '?';

        return $baseWhatsappUrl . $separator . 'text=' . rawurlencode($message);
    }
}
