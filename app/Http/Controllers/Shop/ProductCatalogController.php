<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductCatalogController extends Controller
{
    /**
     * Muestra el catálogo principal de productos/kits.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->paginate(12);

        return view('shop.products.index', compact('products'));
    }

    /**
     * Muestra el detalle individual de un producto o kit.
     */
    public function show($id)
    {
        // Busca el producto activo por id o lanza error 404
        $product = Product::where('is_active', true)->findOrFail($id);

        return view('shop.products.show', compact('product'));
    }
}
