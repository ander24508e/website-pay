<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Payphone redirige aquí cuando el pago es exitoso
    public function success(Request $request)
    {
        $order = Order::findOrFail(session('current_order_id'));

        Transaction::create([
            'order_id'             => $order->id,
            'payphone_ref'         => $request->transactionId ?? null,
            'amount'               => $order->total,
            'status'               => 'approved',
            'response_payload'     => $request->all(), // guarda todo
            'client_transaction_id'=> $request->clientTransactionId ?? null,
        ]);

        $order->update([
            'status'                  => 'paid',
            'payphone_transaction_id' => $request->transactionId ?? null,
        ]);

        session()->forget(['carrito', 'current_order_id']);

        return redirect()->route('orden.confirmacion', $order)
            ->with('success', '¡Pago realizado con éxito!');
    }

    // Payphone redirige aquí cuando cancela
    public function cancel(Request $request)
    {
        $order = Order::findOrFail(session('current_order_id'));

        Transaction::create([
            'order_id'         => $order->id,
            'payphone_ref'     => $request->transactionId ?? null,
            'amount'           => $order->total,
            'status'           => 'cancelled',
            'response_payload' => $request->all(),
        ]);

        $order->update(['status' => 'cancelled']);

        return redirect()->route('carrito.index')
            ->with('error', 'Pago cancelado.');
    }

    // Admin — ver todas las transacciones
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
}