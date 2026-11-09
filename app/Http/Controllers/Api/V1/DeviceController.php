<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Hub;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->header('X-Company-Id');

        if (!$companyId) {
            return response()->json(['message' => 'Falta el contexto de la empresa (X-Company-Id)'], 400);
        }

        $belongsToCompany = $request->user()->companies()->where('companies.id', $companyId)->exists();

        if (!$belongsToCompany) {
            return response()->json(['message' => 'No autorizado para ver este Workspace'], 403);
        }

        $hubIds = Hub::where('company_id', $companyId)->pluck('id');

        $devices = Device::whereIn('hub_id', $hubIds)->get()->map(function ($device) {
            return [
                'id' => (string) $device->id,
                'name' => $device->name,
                'type' => $device->type,
                'lastReading' => $device->last_reading ?? 'Sin datos',
                'isOnline' => (bool) ($device->is_active ?? true)
            ];
        });

        return response()->json([
            'data' => $devices
        ], 200);
    }

    public function telemetry(Request $request, $id)
    {
        $companyId = $request->header('X-Company-Id');

        if (!$companyId) {
            return response()->json(['message' => 'Falta el contexto de la empresa (X-Company-Id)'], 400);
        }

        $belongsToCompany = $request->user()->companies()->where('companies.id', $companyId)->exists();
        if (!$belongsToCompany) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $hubIds = \App\Models\Hub::where('company_id', $companyId)->pluck('id');
        $device = Device::whereIn('hub_id', $hubIds)->find($id);

        if (!$device) {
            return response()->json(['message' => 'Dispositivo no encontrado o sin acceso'], 404);
        }

        $telemetry = $device->telemetryReadings()
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'value' => (float) $reading->value,
                    'unit' => $reading->unit ?? '',
                    'timestamp' => $reading->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'device_id' => $device->id,
            'device_name' => $device->name,
            'readings' => $telemetry
        ], 200);
    }
}
