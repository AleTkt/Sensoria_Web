<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Muestra la vista del carrito de compras.
     */
    public function index()
    {
        // Recuperamos el carrito de la sesión actual; si no hay nada, devuelve un arreglo vacío.
        $cart = session()->get('cart', []);

        return view('shop.cart.index', compact('cart'));
    }

    /**
     * Añade un producto al carrito almacenado en sesión.
     */
    public function add(Request $request, $id)
    {
        // 1. Validamos que el producto exista y esté activo en la BD
        $product = Product::where('is_active', true)->findOrFail($id);
        $quantity = $request->input('quantity', 1);

        // 2. Recuperamos el carrito actual
        $cart = session()->get('cart', []);

        // 3. Si el producto ya está en el carrito, sumamos la cantidad
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            // Si es nuevo en el carrito, registramos sus propiedades esenciales
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'image_path' => $product->image_path,
            ];
        }

        // 4. Actualizamos la sesión con el nuevo arreglo
        session()->put('cart', $cart);

        // 5. Redirigimos a la vista del carrito
        return redirect()->route('shop.cart.index')->with('success', 'Producto añadido al carrito correctamente.');
    }
}
