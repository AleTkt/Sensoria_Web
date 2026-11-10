<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Obtener alertas de una empresa específica.
     */
    public function index(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer'
        ]);

        $alerts = Alert::where('company_id', $request->company_id)
            ->with(['device', 'hub'])
            ->orderBy('is_resolved', 'asc')
            ->latest()
            ->get()->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'severity' => $alert->severity,
                    'title' => $alert->title,
                    'message' => $alert->message,
                    'is_resolved' => $alert->is_resolved,
                    'created_at' => $alert->created_at->format('Y-m-d H:i:s'),
                    'device_name' => $alert->device ? $alert->device->name : 'Hub/Sistema',
                ];
            });

        return response()->json(['data' => $alerts]);
    }

    /**
     * Marcar alerta como resuelta desde Android.
     */
    public function resolve(Request $request, $id)
    {
        $alert = Alert::findOrFail($id);
        $alert->is_resolved = true;
        $alert->resolved_at = now();
        $alert->save();

        return response()->json(['message' => 'Alerta resuelta']);
    }
}
