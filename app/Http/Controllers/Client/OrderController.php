<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Muestra el historial de pedidos de la empresa actual (PYME).
     */
    public function index()
    {
        $companyId = session('current_company_id');

        // Traemos las órdenes con su código de activación, ordenadas por la más reciente
        $orders = Order::with('activationCode')
            ->where('company_id', $companyId)
            ->latest('created_at')
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    /**
     * Muestra el detalle de una compra específica y su código de activación.
     */
    public function show(string $id)
    {
        $companyId = session('current_company_id');

        // Aseguramos que la orden pertenezca a la empresa en sesión
        $order = Order::with(['items.product', 'items.kit', 'activationCode', 'paymentMethod'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return view('client.orders.show', compact('order'));
    }

    /**
     * Descarga el comprobante de compra en PDF para una orden específica.
     */
    public function downloadPdf(string $id)
    {
        $companyId = session('current_company_id');

        // Buscamos la orden asegurando que pertenezca a la empresa en sesión y cargando sus relaciones
        $order = Order::with(['items.product', 'items.kit', 'activationCode', 'paymentMethod', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $company = $order->company;
        $activationCode = $order->activationCode;

        // Generamos el PDF utilizando la vista que creaste (pdf.order-receipt)
        $pdf = \PDF::loadView('pdf.order-receipt', compact('order', 'company', 'activationCode'));

        // Descarga el archivo de forma local con el número de orden como nombre
        return $pdf->download('Comprobante-' . $order->order_number . '.pdf');
    }
}
