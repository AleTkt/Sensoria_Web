<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Http\Requests\StorePaymentMethodRequest;

class WalletController extends Controller
{
    /**
     * Muestra la vista de billetera, métodos de pago e historial del cliente.
     */
    public function index()
    {
        $userId = Auth::id();
        $companyId = session('current_company_id'); // <-- Obtenemos la empresa activa en sesión

        // 1. Obtener métodos de pago del usuario
        $paymentMethods = PaymentMethod::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Obtener el historial de transacciones de la empresa activa con su tarjeta asociada
        $transactions = collect();
        if ($companyId) {
            $transactions = Order::where('company_id', $companyId)
                ->where('status', 'PAGADO')
                ->with('paymentMethod')
                ->orderBy('paid_at', 'desc')
                ->get();
        }

        return view('shop.wallet.index', compact('paymentMethods', 'transactions'));
    }

    /**
     * Almacena un nuevo método de pago para el usuario autenticado.
     */
    public function storePaymentMethod(StorePaymentMethodRequest $request)
    {
        $cardNumber = preg_replace('/\D/', '', $request->input('card_number'));
        $last4 = substr($cardNumber, -4);

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

    /**
     * Establece un método de pago como predeterminado.
     */
    public function setDefaultPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        PaymentMethod::where('user_id', Auth::id())->update(['is_default' => false]);
        $paymentMethod->update(['is_default' => true]);

        return redirect()->route('wallet.index')->with('success', 'Método de pago predeterminado actualizado correctamente.');
    }
}
