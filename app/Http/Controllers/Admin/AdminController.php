<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Estadísticas generales de plataforma y usuarios
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_users' => User::count(),
            'staff_members' => User::whereIn('global_role', ['ADMIN', 'SOPORTE'])->count(),

            // Estadísticas del Módulo de Productos
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_stock' => Product::sum('stock'),
            'avg_price' => Product::avg('price'),
        ];

        // Datos para Chart.js: Conteo de productos agrupados por categoría
        $productsByCategory = Product::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $chartCategories = $productsByCategory->keys();
        $chartTotals = $productsByCategory->values();

        return view('admin.dashboard', compact('stats', 'chartCategories', 'chartTotals'));
    }
}
