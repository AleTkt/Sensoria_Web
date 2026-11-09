<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hub;
use App\Models\Device;

class WorkspaceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('current_company_id');

        if (!$companyId) {
            return redirect()->route('company.select');
        }

        $company = Auth::user()->companies()->findOrFail($companyId);

        $hubsCount = Hub::where('company_id', $company->id)->count();
        $hubIds = Hub::where('company_id', $company->id)->pluck('id');

        $totalDevices = Device::whereIn('hub_id', $hubIds)->count();
        $onlineDevices = Device::whereIn('hub_id', $hubIds)->where('is_active', true)->count();

        $openTicketsCount = 0;

        return view('workspace.dashboard', compact(
            'hubsCount',
            'totalDevices',
            'onlineDevices',
            'openTicketsCount'
        ));
    }
    public function devices(Request $request)
    {
        $companyId = session('current_company_id');

        $hubIds = \App\Models\Hub::where('company_id', $companyId)->pluck('id');
        $devices = \App\Models\Device::whereIn('hub_id', $hubIds)->get();

        return view('workspace.devices.index', compact('devices'));
    }

    public function metrics(Request $request, $id)
    {
        $companyId = session('current_company_id');
        $hubIds = \App\Models\Hub::where('company_id', $companyId)->pluck('id');

        // Verificamos que el dispositivo pertenezca a la empresa
        $device = \App\Models\Device::whereIn('hub_id', $hubIds)->findOrFail($id);

        // Obtenemos las lecturas históricas
        $readings = $device->telemetryReadings()->latest()->take(30)->get();

        return view('workspace.devices.metrics', compact('device', 'readings'));
    }

}
