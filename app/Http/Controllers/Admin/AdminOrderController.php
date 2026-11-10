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

    /**
     * Exporta el listado filtrado de órdenes en formato CSV para reportes gerenciales.
     */
    public function exportCsv(Request $request)
    {
        // 1. Reutilizamos la misma lógica de consulta y filtros del index
        $query = Order::with(['company', 'paymentMethod', 'activationCode']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->latest('created_at')->get();

        // 2. Preparamos los headers y la respuesta CSV en streaming
        $filename = 'reporte-pedidos-sensoria-' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // Añadir el BOM para asegurar correcta lectura de acentos en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados de columnas del CSV
            fputcsv($file, ['Numero de Orden', 'Empresa (PYME)', 'Fecha y Hora', 'Total ($)', 'Estado', 'Codigo de Activacion', 'Metodo de Pago']);

            // Datos de cada orden
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->company->trade_name ?? 'N/A',
                    $order->created_at->format('d/m/Y H:i'),
                    number_format($order->total_amount, 2, '.', ''),
                    $order->status,
                    $order->activationCode->code ?? 'N/A',
                    $order->paymentMethod ? strtoupper($order->paymentMethod->brand) . ' (' . $order->paymentMethod->last_4_digits . ')' : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
