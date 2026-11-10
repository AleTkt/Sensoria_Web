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
}
