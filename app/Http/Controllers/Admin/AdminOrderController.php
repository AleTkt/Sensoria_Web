<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    /**
     * Muestra todos los pedidos de la plataforma para los administradores.
     */
    public function index()
    {
        // Lógica futura: Listar todos los pedidos con paginación y filtros.
        // return view('crm.orders.index', compact('orders'));
    }

    /**
     * Muestra el detalle completo de un pedido, incluyendo cliente y facturación.
     */
    public function show(string $id)
    {
        // Lógica futura: Cargar el pedido con sus items y cliente.
        // return view('crm.orders.show', compact('order'));
    }

    /**
     * Actualiza el estado de un pedido (ej. de PAGADO a ENVIADO).
     */
    public function update(Request $request, string $id)
    {
        // Lógica futura: Validar y actualizar el campo 'status'.
    }
}
