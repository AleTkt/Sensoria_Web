<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Kit;

class CartController extends Controller
{
    /**
     * Muestra la vista del carrito de compras.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart.index', compact('cart'));
    }

    /**
     * Añade un producto o un kit al carrito almacenado en sesión.
     */
    public function add(Request $request, $id)
    {
        $quantity = $request->input('quantity', 1);
        $item = null;
        $isKit = false;

        // 1. Buscamos primero si el ID corresponde a un Kit activo
        $kit = Kit::where('is_active', true)->find($id);

        if ($kit) {
            $item = $kit;
            $isKit = true;
        } else {
            // 2. Si no es un kit, buscamos como producto individual activo
            $item = Product::where('is_active', true)->findOrFail($id);
            $isKit = false;
        }

        // 3. Recuperamos el carrito actual de la sesión
        $cart = session()->get('cart', []);

        // Generamos una clave única en el carrito para evitar colidencias si un producto y un kit compartieran ID (ej: kit_1 o prod_1, o mantenemos el ID simple si son independientes)
        $cartKey = $isKit ? 'kit_' . $item->id : 'prod_' . $item->id;

        // 4. Si el ítem ya está en el carrito, sumamos la cantidad
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Registramos sus propiedades esenciales incluyendo la bandera is_kit
            $cart[$cartKey] = [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $quantity,
                'price' => $item->price,
                'image_path' => $item->image_path,
                'is_kit' => $isKit,
            ];
        }

        // 5. Actualizamos la sesión
        session()->put('cart', $cart);

        return redirect()->route('shop.cart.index')->with('success', 'Solución IoT añadida al carrito correctamente.');
    }


    /**
     * Actualiza la cantidad de un ítem en el carrito (incrementar o disminuir).
     */
    public function updateQuantity(Request $request, $key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $action = $request->input('action');

            if ($action === 'increase') {
                $cart[$key]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$key]['quantity']--;
                // Si la cantidad llega a 0, eliminamos el ítem automáticamente
                if ($cart[$key]['quantity'] <= 0) {
                    unset($cart[$key]);
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index')->with('success', 'Cantidad actualizada correctamente.');
    }

    /**
     * Elimina un ítem específico del carrito.
     */
    public function removeItem($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index')->with('success', 'Solución IoT eliminada del carrito.');
    }
}
