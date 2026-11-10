<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hub;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ActivationCode;

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

        // Cargamos los dispositivos junto con su última lectura de telemetría
        $devices = \App\Models\Device::whereIn('hub_id', $hubIds)
            ->with(['telemetryReadings' => function ($query) {
                $query->latest('recorded_at')->limit(1);
            }])
            ->get();

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

    public function redeem(Request $request)
    {
        $request->validate(['activation_code' => 'required|string|max:50']);
        $companyId = session('current_company_id');

        if (!$companyId) return back()->with('error', 'No hay una empresa activa.');

        // 1. Iniciamos transacción segura
        return DB::transaction(function () use ($request, $companyId) {
            // Bloqueamos la fila (lockForUpdate) para que ninguna otra petición la toque hasta terminar
            $activation = ActivationCode::where('code', $request->activation_code)
                ->lockForUpdate()
                ->first();

            if (!$activation) {
                return back()->with('error', 'El código ingresado no existe.');
            }

            if ($activation->status !== 'PENDIENTE') {
                return back()->with('error', 'Este código ya ha sido canjeado.');
            }

        // 2. Extraer la orden relacionada
        $order = Order::with('items.product', 'items.kit.products')->find($activation->order_id);
        if (!$order || $order->company_id !== $companyId) {
            return back()->with('error', 'El código no corresponde a tu empresa o la orden es inválida.');
        }

        // 3. Buscar el Hub dormido de la empresa (el que creamos en el registro)
        $hub = Hub::where('company_id', $companyId)->first();
        if (!$hub) {
            return back()->with('error', 'No se encontró un Hub principal para tu empresa.');
        }

        // 4. Inyección: Crear los dispositivos reales basados en la orden (Ignorando Hubs)
        foreach ($order->items as $item) {
            // Si el ítem es un Kit, iterar sobre sus productos
            if ($item->kit) {
                foreach ($item->kit->products as $product) {

                    // VALIDACIÓN: Ignoramos los productos tipo HUB
                    if (strtoupper($product->type) === 'HUB' || strtoupper($product->category) === 'HUB') {
                        continue; // Salta al siguiente producto sin insertarlo como sensor
                    }

                    // Multiplicamos la cantidad del kit por la cantidad del producto en el kit
                    $totalQuantity = $item->quantity * $product->pivot->quantity;

                    for ($i = 0; $i < $totalQuantity; $i++) {
                        Device::create([
                            'hub_id' => $hub->id,
                            'device_code' => 'DEV-' . strtoupper(Str::random(8)),
                            'name' => $product->name,
                            'type' => $product->type ?? 'SENSOR',
                            'is_active' => true,
                        ]);
                    }
                }
            }
            // Si el ítem es un producto individual
            elseif ($item->product) {

                if (strtoupper($item->product->type) === 'HUB' || strtoupper($item->product->category) === 'HUB') {
                    continue; // Salta al siguiente iterador
                }

                for ($i = 0; $i < $item->quantity; $i++) {
                    Device::create([
                        'hub_id' => $hub->id,
                        'device_code' => 'DEV-' . strtoupper(Str::random(8)), // <-- LÍNEA AGREGADA
                        'name' => $item->product->name,
                        'type' => $item->product->type ?? 'SENSOR',
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 5. El Despertar del Hub: Asignar api_token y estado ONLINE
        if ($hub->status === 'DESCONECTADO') {
            $hub->api_token = Str::random(40);
            $hub->ip_address = '192.168.1.' . rand(10, 250);
            $hub->status = 'ONLINE';
            $hub->last_ping_at = now();
            $hub->save();
        }

        // 6. Marcar el código como canjeado
        $activation->status = 'CANJEADO';
        $activation->save();

        return back()->with('success', '¡Hardware activado con éxito! Tus dispositivos comprados ya están vinculados a tu Hub.');
    });}}

