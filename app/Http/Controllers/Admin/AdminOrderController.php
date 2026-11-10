<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Muestra la lista global de todos los pedidos con soporte de filtros y métricas.
     */
    public function index(Request $request)
    {
        // 1. Consulta base con relaciones necesarias
        $query = Order::with(['company', 'paymentMethod', 'activationCode']);

        // 2. Aplicar Filtro de Búsqueda (Número de orden o nombre/comercio de la empresa)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('company', function ($companyQuery) use ($searchTerm) {
                        $companyQuery->where('trade_name', 'like', "%{$searchTerm}%")
                            ->orWhere('business_name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // 3. Aplicar Filtro por Estado
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 4. Aplicar Filtro por Rango de Fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // 5. Métricas globales para las tarjetas del dashboard
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'PAGADO')->sum('total_amount'),
            'paid_orders' => Order::where('status', 'PAGADO')->count(),
            'pending_orders' => Order::where('status', 'PENDIENTE')->count(),
        ];

        // 6. Obtener los resultados paginados manteniendo los parámetros en los links de paginación
        $orders = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders', 'stats'));
    }
}
