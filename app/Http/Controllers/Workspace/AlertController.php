<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Hub;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Muestra el listado de alertas de la empresa actual.
     */
    public function index(Request $request)
    {
        $companyId = session('current_company_id');

        if (!$companyId) {
            return redirect()->route('company.select');
        }

        // Cargar alertas de los Hubs pertenecientes a la empresa
        $alerts = Alert::where('company_id', $companyId)
            ->with(['device', 'hub'])
            ->latest()
            ->paginate(15);

        return view('workspace.alerts.index', compact('alerts'));
    }

    /**
     * Marca una alerta como resuelta.
     */
    public function resolve(Request $request, $id)
    {
        $companyId = session('current_company_id');

        $alert = Alert::where('company_id', $companyId)->findOrFail($id);
        $alert->is_resolved = true;
        $alert->resolved_at = now();
        $alert->save();

        return back()->with('success', 'La alerta ha sido marcada como resuelta.');
    }
}
