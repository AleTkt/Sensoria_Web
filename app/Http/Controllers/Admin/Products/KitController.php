<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Kit;


class KitController extends Controller
{
    public function index()
    {
        // Traemos todos los kits comerciales
        $kits = Kit::orderBy('id', 'desc')->get();
        return view('admin.catalog.kits.index', compact('kits'));
    }
}
