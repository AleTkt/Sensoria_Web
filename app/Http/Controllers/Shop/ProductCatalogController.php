<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Kit;

class ProductCatalogController extends Controller
{
    /**
     * Muestra el catálogo principal con productos y kits activos.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->paginate(8);
        $kits = Kit::where('is_active', true)->with('products')->get();

        return view('shop.products.index', compact('products', 'kits'));
    }

    /**
     * Muestra el detalle individual de un producto o un kit.
     */
    public function show($id)
    {
        // Intentamos buscar primero si es un Kit
        $kit = Kit::where('is_active', true)->with('products')->find($id);

        if ($kit) {
            return view('shop.products.show', [
                'item' => $kit,
                'isKit' => true
            ]);
        }

        // Si no es un kit, buscamos como producto individual
        $product = Product::where('is_active', true)->findOrFail($id);

        return view('shop.products.show', [
            'item' => $product,
            'isKit' => false
        ]);
    }
}
