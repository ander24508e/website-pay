<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Solo compras — el perfil ya lo maneja ProfileController
    public function compras()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.itemable', 'transaction'])
            ->latest()
            ->get();

        return view('customer.compras', compact('orders'));
    }
}