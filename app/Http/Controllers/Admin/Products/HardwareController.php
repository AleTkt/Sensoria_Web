<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class HardwareController extends Controller
{
    public function index()
    {
        // Traemos todo el hardware base
        $hardware = Product::orderBy('id', 'desc')->get();
        return view('admin.catalog.hardware.index', compact('hardware'));
    }
}
