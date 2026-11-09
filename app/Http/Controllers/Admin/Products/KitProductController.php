<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use Illuminate\Http\Request;

class KitProductController extends Controller
{
    /**
     * Muestra la lista de kits para gestionar sus productos.
     */
    public function index()
    {
        // Obtenemos los kits con sus productos relacionados, ordenados por ID descendente
        $kits = Kit::with('products')->orderBy('id', 'desc')->get();

        return view('admin.catalog.products.index', compact('kits'));
    }

    /**
     * Obtener datos de productos en un kit específico (para el modal)
     */
    public function getProductsData($kitId)
    {
        $kit = Kit::with('products')->findOrFail($kitId);

        return response()->json([
            'kit' => $kit,
            'products' => $kit->products
        ]);
    }

    /**
     * Agregar múltiples productos al kit de una vez
     */
    public function batchAddProducts(Request $request, $kitId)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'required|integer|exists:pgsql.commerce.products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $kit = Kit::findOrFail($kitId);
        $addedCount = 0;
        $skippedCount = 0;

        foreach ($validated['products'] as $productId) {
            if ($kit->products()->where('product_id', $productId)->exists()) {
                $skippedCount++;
                continue;
            }

            $kit->products()->attach($productId, [
                'quantity' => $validated['quantity']
            ]);
            $addedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Se agregaron {$addedCount} productos al kit." .
                ($skippedCount > 0 ? " ({$skippedCount} ya existían)" : '')
        ]);
    }

    /**
     * Actualizar cantidad de un producto en el kit (PATCH)
     */
    public function updateQuantity(Request $request, $kitId, $productId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $kit = Kit::findOrFail($kitId);
        $kit->products()->updateExistingPivot($productId, [
            'quantity' => $validated['quantity']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada exitosamente.'
        ]);
    }

    /**
     * Eliminar un producto del kit (DELETE)
     */
    public function removeProduct($kitId, $productId)
    {
        $kit = Kit::findOrFail($kitId);
        $kit->products()->detach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del kit exitosamente.'
        ]);
    }
}
