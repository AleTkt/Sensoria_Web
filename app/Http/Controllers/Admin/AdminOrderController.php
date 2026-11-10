<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Muestra la lista global de todos los pedidos y sus métricas resumidas.
     */
    public function index()
    {
        // Métricas de órdenes para las tarjetas superiores
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'PAGADO')->sum('total_amount'),
            'paid_orders' => Order::where('status', 'PAGADO')->count(),
            'pending_orders' => Order::where('status', 'PENDIENTE')->count(),
        ];

        // Cargamos las órdenes globales con su empresa, método de pago y código de activación
        $orders = Order::with(['company', 'paymentMethod', 'activationCode'])
            ->latest('created_at')
            ->paginate(15);

        return view('admin.orders.index', compact('orders', 'stats'));
    }
}
