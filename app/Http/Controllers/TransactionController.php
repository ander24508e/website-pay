<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class TransactionController extends Controller
{
    // Payphone redirige aqui cuando el pago es exitoso
    public function success(Request $request)
    {
        $order = $this->resolveOrderFromCallback($request);

        if (!$order) {
            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo identificar la orden para confirmar el pago.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('orden.confirmacion', $order)
                ->with('success', 'Esta orden ya fue pagada.');
        }

        // Verificar el pago con Payphone antes de confirmar
        try {
            $verification = Http::timeout(20)
                ->withToken(config('services.payphone.token'))
                ->post(config('services.payphone.base_url') . '/api/button/Confirm', [
                    'id'                  => $request->id,
                    'clientTransactionId' => $request->clientTransactionId,
                ]);

            $payload = $verification->json() ?? [];
        } catch (Throwable $e) {
            $payload = [
                'error' => 'payphone_confirm_failed',
                'message' => $e->getMessage(),
            ];
        }

        $status = ($payload['transactionStatus'] ?? '') === 'Approved' ? 'approved' : 'rejected';

        $payphoneRef = $request->id ?? $request->transactionId ?? null;

        $this->upsertTransaction($order, [
            'order_id'              => $order->id,
            'payphone_ref'          => $payphoneRef,
            'amount'                => $order->total,
            'status'                => $status,
            'response_payload'      => $payload,
            'client_transaction_id' => $request->clientTransactionId ?? null,
        ]);

        if ($status === 'approved') {
            $order->update([
                'status'                  => 'paid',
                'payphone_transaction_id' => $payphoneRef,
            ]);
            session()->forget(['carrito', 'current_order_id']);

            return redirect()->route('orden.confirmacion', $order)
                ->with('success', 'Pago realizado con exito.');
        }

        $order->update(['status' => 'failed']);

        return redirect()->route('carrito.index')
            ->with('error', 'El pago fue rechazado. Intenta de nuevo.');
    }

    // Payphone redirige aqui cuando cancela
    public function cancel(Request $request)
    {
        $order = $this->resolveOrderFromCallback($request);

        if (!$order) {
            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo identificar la orden cancelada.');
        }

        $payphoneRef = $request->transactionId ?? $request->id ?? null;

        $this->upsertTransaction($order, [
            'order_id'              => $order->id,
            'payphone_ref'          => $payphoneRef,
            'amount'                => $order->total,
            'status'                => 'cancelled',
            'response_payload'      => $request->all(),
            'client_transaction_id' => $request->clientTransactionId ?? null,
        ]);

        if ($order->status !== 'paid') {
            $order->update(['status' => 'cancelled']);
        }

        return redirect()->route('carrito.index')
            ->with('error', 'Pago cancelado.');
    }

    // Admin - ver todas las transacciones
    public function index()
    {
        $transactions = Transaction::with('order.user')->latest()->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('order.user');
        return view('admin.transactions.show', compact('transaction'));
    }

    private function resolveOrderFromCallback(Request $request): ?Order
    {
        $orderId = session('current_order_id');

        if (!$orderId && $request->filled('clientTransactionId')) {
            if (preg_match('/order-(\d+)-\d+/', (string) $request->clientTransactionId, $matches)) {
                $orderId = (int) $matches[1];
            }
        }

        if (!$orderId && auth()->check()) {
            $orderId = Order::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'failed', 'cancelled'])
                ->latest('id')
                ->value('id');
        }

        return $orderId ? Order::find($orderId) : null;
    }

    private function upsertTransaction(Order $order, array $data): Transaction
    {
        $existing = Transaction::query()
            ->where('order_id', $order->id)
            ->where('payphone_ref', $data['payphone_ref'] ?? null)
            ->where('client_transaction_id', $data['client_transaction_id'] ?? null)
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->fill($data)->save();
            return $existing;
        }

        return Transaction::create($data);
    }
}
