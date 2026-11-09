<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKitRequest; // <-- 1. Importamos el Request
use App\Models\Kit;

class KitController extends Controller
{
    public function index()
    {
        $kits = Kit::orderBy('id', 'desc')->get();
        return view('admin.catalog.kits.index', compact('kits'));
    }

    // 2. Cambiamos Request $request por StoreKitRequest $request
    public function store(StoreKitRequest $request)
    {
        // 3. Ya no necesitamos $request->validate(), Laravel lo hizo automáticamente.
        // Solo obtenemos los datos validados.
        $validated = $request->validated();

        // Manejo del checkbox
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Creación del registro
        Kit::create($validated);

        // 4. Redirección con mensaje de éxito en la sesión (Flash message)
        return redirect()->route('admin.catalog.kits')
            ->with('success', '¡Kit comercial creado exitosamente!');
    }

    public function update(\App\Http\Requests\UpdateKitRequest $request, $id)
    {
        $kit = Kit::findOrFail($id);

        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $kit->update($validated);

        return redirect()->route('admin.catalog.kits')
            ->with('success', '¡Kit comercial actualizado exitosamente!');
    }

    public function toggleStatus($id)
    {
        $kit = Kit::findOrFail($id);
        $kit->is_active = !$kit->is_active;
        $kit->save();

        $action = $kit->is_active ? 'activado' : 'desactivado';
        return redirect()->route('admin.catalog.kits')
            ->with('success', "Kit {$action} exitosamente.");
    }
}
