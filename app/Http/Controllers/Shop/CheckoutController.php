<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ActivationCode;
use App\Models\Company;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\CorreoService;

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

        $request->validate([
            'payment_method_id' => ['required', \Illuminate\Validation\Rule::exists(PaymentMethod::class, 'id')],
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
            // Almacenamos el resultado de la transacción para usar la Orden y el Código afuera
            $transactionData = DB::transaction(function () use ($cart, $totalAmount, $companyId, $request) {
                // 2. Crear la Orden
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'company_id' => $companyId,
                    'total_amount' => $totalAmount,
                    'status' => 'PAGADO',
                    'payment_method_id' => $request->payment_method_id,
                    'paid_at' => now(),
                ]);

                // 3. Crear los Detalles de la Orden
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

                // 4. Generar el código de activación
                $activationCode = ActivationCode::create([
                    'order_id' => $order->id,
                    'code' => 'SNSR-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                    'status' => 'PENDIENTE',
                ]);

                return ['order' => $order, 'activationCode' => $activationCode];
            });

            $order = $transactionData['order'];
            $activationCode = $transactionData['activationCode'];

            // 5. Cargar relaciones necesarias para el PDF y prepararlo en memoria
            $order->load(['items.product', 'items.kit', 'paymentMethod']);
            $company = Company::find($companyId);

            $pdf = Pdf::loadView('pdf.order-receipt', compact('order', 'company', 'activationCode'));

            // 6. Preparar y enviar el correo al cliente
            $cuerpoHtml = '
            <div style="background-color: #FAFAF7; color: #374151; font-family: system-ui, sans-serif; padding: 40px 20px;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden;">
                    <div style="background-color: #1B3D1A; padding: 25px; text-align: center;">
                        <h2 style="color: #ffffff; margin: 0; font-size: 24px;">Sensoría <span>IoT</span></h2>
                    </div>
                    <div style="padding: 30px;">
                        <h3 style="color: #1B3D1A; margin-top: 0;">¡Gracias por tu compra, ' . Auth::user()->name . '!</h3>
                        <p style="line-height: 1.6;">Tu orden <strong>' . $order->order_number . '</strong> se ha procesado exitosamente. Adjunto a este correo encontrarás el PDF con tu comprobante detallado.</p>

                        <p style="line-height: 1.6;">Para enlazar y activar tus nuevos equipos en tu Hub, ingresa el siguiente código en la sección de Activación de tu panel de control:</p>

                        <div style="text-align: center; margin: 30px 0; background-color: #f3f4f6; padding: 15px; border-radius: 6px; border: 1px dashed #589445;">
                            <strong style="font-size: 22px; color: #589445; letter-spacing: 2px;">' . $activationCode->code . '</strong>
                        </div>

                        <p style="font-size: 13px; color: #9ca3af; margin-bottom: 0;">Si tienes dudas sobre el proceso de instalación, no dudes en contactar a soporte.</p>
                    </div>
                </div>
            </div>';

            $adjunto = [
                'data' => $pdf->output(), // Renderiza el PDF directo de la memoria
                'nombre' => 'Comprobante_Compra_' . $order->order_number . '.pdf',
                'mime' => 'application/pdf'
            ];

            CorreoService::enviarCorreo(
                Auth::user()->email,
                "Comprobante de Orden " . $order->order_number . " - Sensoría IoT",
                $cuerpoHtml,
                $adjunto
            );

            // 7. Limpiar el carrito
            session()->forget('cart');

            return redirect()->route('client.orders.index') // Ajusta esta ruta a donde sea pertinente
            ->with('success', '¡Compra exitosa! Hemos enviado tu comprobante y código de activación a tu correo electrónico.');

        } catch (\Exception $e) {
            \Log::error("Error en checkout: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al procesar tu compra. Por favor, intenta de nuevo.');
        }
    }
}
