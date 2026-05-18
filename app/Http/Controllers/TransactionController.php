<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function success(Request $request)
    {
        $clientTransactionId = (string) ($request->input('clientTransactionId') ?: $request->input('clientTxId') ?: '');
        $orderId = session('current_order_id');
        $order = $orderId ? Order::find($orderId) : null;

        if (!$order && $clientTransactionId !== '') {
            $existingTx = Transaction::query()
                ->where('client_transaction_id', $clientTransactionId)
                ->latest('id')
                ->first();

            if ($existingTx) {
                $order = Order::find($existingTx->order_id);
            }
        }

        if (!$order) {
            Log::error('Payphone success: orden no encontrada', [
                'order_id' => $orderId,
                'client_transaction_id' => $clientTransactionId,
                'payload' => $request->all(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Orden no encontrada.');
        }

        $verificacion = $this->verificarPagoPayphone(
            $request->input('id'),
            $clientTransactionId
        );

        $payload = $verificacion ?? $request->all();
        $aprobado = ($payload['transactionStatus'] ?? '') === 'Approved';
        $status = $aprobado ? 'approved' : 'rejected';

        $tx = null;
        if ($clientTransactionId !== '') {
            $tx = Transaction::query()
                ->where('client_transaction_id', $clientTransactionId)
                ->latest('id')
                ->first();
        }

        if ($tx) {
            $tx->update([
                'payphone_ref' => $request->input('id'),
                'status' => $status,
                'response_payload' => $payload,
            ]);
        } else {
            Transaction::create([
                'order_id' => $order->id,
                'payphone_ref' => $request->input('id'),
                'amount' => $order->total,
                'status' => $status,
                'response_payload' => $payload,
                'client_transaction_id' => $clientTransactionId !== '' ? $clientTransactionId : null,
            ]);
        }

        if ($aprobado) {
            $order->update([
                'status' => 'paid',
                'payphone_transaction_id' => $request->input('id'),
            ]);

            session()->forget(['carrito', 'current_order_id']);
            session()->regenerateToken();

            return redirect()->route('orden.confirmacion', $order)
                ->with('success', 'Pago realizado con exito.');
        }

        $order->update(['status' => 'failed']);

        Log::warning('Payphone pago rechazado', [
            'order_id' => $order->id,
            'payload' => $payload,
        ]);

        return redirect()->route('carrito.index')
            ->with('error', 'El pago fue rechazado por Payphone. Intenta de nuevo.');
    }

    public function cancel(Request $request)
    {
        $clientTransactionId = (string) ($request->input('clientTransactionId') ?: $request->input('clientTxId') ?: '');
        $orderId = session('current_order_id');
        $order = $orderId ? Order::find($orderId) : null;

        if (!$order && $clientTransactionId !== '') {
            $existingTx = Transaction::query()
                ->where('client_transaction_id', $clientTransactionId)
                ->latest('id')
                ->first();

            if ($existingTx) {
                $order = Order::find($existingTx->order_id);
            }
        }

        if ($order) {
            $tx = null;
            if ($clientTransactionId !== '') {
                $tx = Transaction::query()
                    ->where('client_transaction_id', $clientTransactionId)
                    ->latest('id')
                    ->first();
            }

            if ($tx) {
                $tx->update([
                    'payphone_ref' => $request->input('id'),
                    'status' => 'cancelled',
                    'response_payload' => $request->all(),
                ]);
            } else {
                Transaction::create([
                    'order_id' => $order->id,
                    'payphone_ref' => $request->input('id'),
                    'amount' => $order->total,
                    'status' => 'cancelled',
                    'response_payload' => $request->all(),
                    'client_transaction_id' => $clientTransactionId !== '' ? $clientTransactionId : null,
                ]);
            }

            $order->update(['status' => 'cancelled']);
        }

        session()->forget('current_order_id');

        return redirect()->route('carrito.index')
            ->with('error', 'Cancelaste el pago. Tu carrito sigue guardado.');
    }

    private function verificarPagoPayphone(?string $id, ?string $clientTransactionId): ?array
    {
        if (!$id || !$clientTransactionId) {
            return null;
        }

        try {
            $headers = [
                'Authorization' => 'Bearer ' . config('services.payphone.token'),
                'Content-Type' => 'application/json',
            ];

            // 1) Confirm para boton de redireccion
            $response = Http::withHeaders($headers)->post(config('services.payphone.base_url') . '/api/button/Confirm', [
                'id' => $id,
                'clientTransactionId' => $clientTransactionId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Payphone button confirm failed, trying payment-box confirm', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // 2) Fallback para cajita de pagos
            $boxResponse = Http::withHeaders($headers)->post('https://paymentbox.payphonetodoesposible.com/api/confirm', [
                'id' => (int) $id,
                'clientTxId' => $clientTransactionId,
            ]);

            if ($boxResponse->successful()) {
                return $boxResponse->json();
            }

            Log::error('Payphone confirm failed in both endpoints', [
                'button_status' => $response->status(),
                'button_body' => $response->body(),
                'box_status' => $boxResponse->status(),
                'box_body' => $boxResponse->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Payphone confirm exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $transactions = Transaction::with('order.user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('id', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('payphone_ref', 'like', "%{$search}%")
                        ->orWhere('client_transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($orderQuery) use ($search) {
                            $orderQuery->where('id', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('order.user');
        return view('admin.transactions.show', compact('transaction'));
    }
}
