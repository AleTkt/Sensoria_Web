<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Muestra la vista de billetera e historial de pagos del cliente.
     */
    public function index()
    {
        // Aquí posteriormente podremos inyectar los datos de transacciones o saldo reales del usuario.
        return view('shop.wallet.index');
    }
}
