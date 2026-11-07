<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Estadísticas rápidas para el panel principal del Staff
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_users' => User::count(),
            'staff_members' => User::whereIn('global_role', ['ADMIN', 'SOPORTE'])->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
