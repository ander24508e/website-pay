<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    // ══════════════════════════════════════════
    // CALLBACK — Payphone llama aquí si el pago fue procesado
    // ══════════════════════════════════════════
    public function success(Request $request)
    {
        // 1. Recuperar la orden de la sesión
        $orderId = session('current_order_id');

        if (!$orderId) {
            Log::warning('Payphone success: no order_id en sesión', $request->all());
            return redirect()->route('home')
                ->with('error', 'No se encontró la orden. Contacta soporte.');
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('Payphone success: orden no encontrada', ['order_id' => $orderId]);
            return redirect()->route('home')
                ->with('error', 'Orden no encontrada.');
        }

        // 2. Verificar el pago con Payphone (SIEMPRE verificar — nunca confiar solo en el redirect)
        $verificacion = $this->verificarPagoPayphone(
            $request->input('id'),
            $request->input('clientTransactionId')
        );

        $payload  = $verificacion ?? $request->all();
        $aprobado = ($payload['transactionStatus'] ?? '') === 'Approved';
        $status   = $aprobado ? 'approved' : 'rejected';

        // 3. Guardar la transacción con TODOS los datos de Payphone
        Transaction::create([
            'order_id'              => $order->id,
            'payphone_ref'          => $request->input('id'),
            'amount'                => $order->total,
            'status'                => $status,
            'response_payload'      => $payload,
            'client_transaction_id' => $request->input('clientTransactionId'),
        ]);

        // 4. Actualizar estado de la orden
        if ($aprobado) {
            $order->update([
                'status'                  => 'paid',
                'payphone_transaction_id' => $request->input('id'),
            ]);

            // 5. Limpiar sesión
            session()->forget(['carrito', 'current_order_id']);

            return redirect()->route('orden.confirmacion', $order)
                ->with('success', '¡Pago realizado con éxito!');
        }

        // Pago rechazado
        $order->update(['status' => 'failed']);

        Log::warning('Payphone pago rechazado', [
            'order_id' => $order->id,
            'payload'  => $payload,
        ]);

        return redirect()->route('carrito.index')
            ->with('error', 'El pago fue rechazado por Payphone. Intenta de nuevo o usa otra tarjeta.');
    }

    // ══════════════════════════════════════════
    // CALLBACK — Payphone llama aquí si el usuario cancela
    // ══════════════════════════════════════════
    public function cancel(Request $request)
    {
        $orderId = session('current_order_id');

        if ($orderId) {
            $order = Order::find($orderId);

            if ($order) {
                // Guardar transacción cancelada
                Transaction::create([
                    'order_id'         => $order->id,
                    'payphone_ref'     => $request->input('id'),
                    'amount'           => $order->total,
                    'status'           => 'cancelled',
                    'response_payload' => $request->all(),
                ]);

                $order->update(['status' => 'cancelled']);
            }
        }

        session()->forget('current_order_id');

        return redirect()->route('carrito.index')
            ->with('error', 'Cancelaste el pago. Tu carrito sigue guardado.');
    }

    // ══════════════════════════════════════════
    // PRIVADO — Verificar pago con Payphone API
    // ══════════════════════════════════════════
    private function verificarPagoPayphone(?string $id, ?string $clientTransactionId): ?array
    {
        if (!$id || !$clientTransactionId) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.payphone.token'),
                'Content-Type'  => 'application/json',
            ])->post(config('services.payphone.base_url') . '/api/button/Confirm', [
                'id'                  => $id,
                'clientTransactionId' => $clientTransactionId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Payphone confirm failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Payphone confirm exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ══════════════════════════════════════════
    // ADMIN — Listado de transacciones
    // ══════════════════════════════════════════
    public function index()
    {
        $transactions = Transaction::with('order.user')->latest()->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    // ══════════════════════════════════════════
    // ADMIN — Detalle de una transacción
    // ══════════════════════════════════════════
    public function show(Transaction $transaction)
    {
        $transaction->load('order.user');
        return view('admin.transactions.show', compact('transaction'));
    }
}