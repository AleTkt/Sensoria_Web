<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Hub;
use Illuminate\Http\Request;

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

        $devices = Device::whereIn('hub_id', $hubIds)
            ->with('latestReading') // eager loading: evita una consulta por dispositivo
            ->get()
            ->map(function ($device) {
                return [
                    'id' => (string) $device->id,
                    'name' => $device->name,
                    'type' => $device->type,
                    'lastReading' => $device->latestReading
                        ? $this->formatReading($device->latestReading)
                        : 'Sin datos',
                    'isOnline' => (bool) ($device->is_active ?? true),
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

        $hubIds = Hub::where('company_id', $companyId)->pluck('id');
        $device = Device::whereIn('hub_id', $hubIds)->find($id);

        if (!$device) {
            return response()->json(['message' => 'Dispositivo no encontrado o sin acceso'], 404);
        }

        $telemetry = $device->telemetryReadings()
            ->orderBy('recorded_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(50)
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'value' => (float) $reading->value,
                    'unit' => $reading->unit ?? '',
                    'timestamp' => $reading->recorded_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'device_id' => $device->id,
            'device_name' => $device->name,
            'readings' => $telemetry
        ], 200);
    }

    /**
     * Convierte una lectura en texto legible: "23.50" -> "23.5 °C", "100.00" -> "100 %".
     */
    private function formatReading($reading): string
    {
        $value = rtrim(rtrim(number_format((float) $reading->value, 2, '.', ''), '0'), '.');

        return trim($value . ' ' . ($reading->unit ?? ''));
    }
}
