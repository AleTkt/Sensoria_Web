<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function showAcceptForm($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        return view('auth.accept-invitation', compact('invitation'));
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if the user already exists (in case they already have a Sensoria account)
        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            // Create the new user with CLIENTE global role
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'global_role' => 'CLIENTE',
            ]);
        }

        // Bind the user to the company
        $user->companies()->attach($invitation->company_id, [
            'company_role' => $invitation->role,
            'is_active' => true,
        ]);

        // Delete the used invitation
        $invitation->delete();

        // Log them in and send them to the dashboard
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Te has unido exitosamente al equipo de ' . $invitation->company->trade_name);
    }
}
