<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use App\Http\Requests\StorePaymentMethodRequest; // ¡Asegúrate de importar esto!

class WalletController extends Controller
{
    /**
     * Muestra la vista de billetera, métodos de pago e historial del cliente.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop.wallet.index', compact('paymentMethods'));
    }

    /**
     * Almacena un nuevo método de pago para el usuario autenticado usando FormRequest.
     */
    public function storePaymentMethod(StorePaymentMethodRequest $request) // <-- Cambio aquí
    {
        // Si el código llega hasta aquí, la validación del FormRequest ya fue exitosa.

        // Limpieza del número de tarjeta
        $cardNumber = preg_replace('/\D/', '', $request->input('card_number'));
        $last4 = substr($cardNumber, -4);

        // Detección básica de marca
        $firstDigit = substr($cardNumber, 0, 1);
        $brand = 'visa';
        if ($firstDigit === '5') {
            $brand = 'mastercard';
        } elseif ($firstDigit === '3') {
            $brand = 'amex';
        }

        $hasCards = PaymentMethod::where('user_id', Auth::id())->exists();

        PaymentMethod::create([
            'user_id'         => Auth::id(),
            'cardholder_name' => $request->input('cardholder_name'),
            'brand'           => $brand,
            'last_4_digits'   => $last4,
            'exp_month'       => $request->input('exp_month'),
            'exp_year'        => $request->input('exp_year'),
            'is_default'      => !$hasCards,
        ]);

        return redirect()->route('wallet.index')->with('success', 'Método de pago registrado correctamente.');
    }
}
