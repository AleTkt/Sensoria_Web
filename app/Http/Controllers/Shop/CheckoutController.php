<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ActivationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    /**
     * Procesa la compra del carrito actual.
     */
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.cart.index')->with('error', 'Tu carrito está vacío.');
        }

        // Aquí deberías recibir el ID del método de pago de tu sistema de Billetera.
        // Como estamos simulando, requerimos que el form envíe 'payment_method_id'.
        $request->validate([
            'payment_method_id' => [
                'required',
                'payment_method_id' => ['required', \Illuminate\Validation\Rule::exists(\App\Models\PaymentMethod::class, 'id')],
            ],
        ]);
        $companyId = session('current_company_id');

        if (!$companyId) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para comprar.');
        }

        // 1. Calcular el total del carrito
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        try {
            DB::transaction(function () use ($cart, $totalAmount, $companyId, $request) {
                // 2. Crear la Orden (Estado PAGADO al simular la pasarela)
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'company_id' => $companyId,
                    'total_amount' => $totalAmount,
                    'status' => 'PAGADO', // Asumimos pago exitoso por la billetera
                    'payment_method_id' => $request->payment_method_id,
                    'paid_at' => now(),
                ]);

                // 3. Crear los Detalles de la Orden (Order Items)
                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['is_kit'] ? null : $item['id'],
                        'kit_id' => $item['is_kit'] ? $item['id'] : null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                }

                // 4. GENERAR EL CÓDIGO DE ACTIVACIÓN
                // Este es el puente entre la orden y el Hub físico
                ActivationCode::create([
                    'order_id' => $order->id,
                    'code' => 'SNSR-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                    'status' => 'PENDIENTE',
                ]);
            });

            // 5. Limpiar el carrito tras una compra exitosa
            session()->forget('cart');

            return redirect()->route('client.orders.index') // Ajusta esta ruta a donde muestres el historial de compras
            ->with('success', '¡Compra procesada con éxito! Tu código de activación está disponible en el detalle de la orden.');

        } catch (\Exception $e) {
            // En caso de fallo (ej. la billetera falla), revertimos y notificamos
            return back()->with('error', 'Ocurrió un error al procesar tu compra: ' . $e->getMessage());
        }
    }
}
