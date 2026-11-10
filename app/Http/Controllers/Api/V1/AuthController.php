<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();
        $user->load('companies');

        $token = $user->createToken('sensoria-android-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'companies' => $user->companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->trade_name ?? $company->legal_name,
                        'role' => $company->pivot->company_role,
                    ];
                })
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    public function getMyCompanies(Request $request)
    {
        $user = $request->user();

        $companies = $user->companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->trade_name ?? $company->legal_name,
                'role' => $company->pivot->company_role,
            ];
        })->values()->all();

        return response()->json(['data' => $companies]);
    }
}
