<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        // Traemos todos los usuarios y las empresas a las que pertenecen
        $users = User::with('companies')->latest()->get();

        return view('admin.users.index', compact('users'));
    }
}
