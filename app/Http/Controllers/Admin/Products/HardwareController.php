<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Kit;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
class HardwareController extends Controller
{
    public function index()
    {
        // Traemos todo el hardware base
        $hardware = Product::orderBy('id', 'desc')->get();
        $kits = Kit::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.catalog.hardware.index', compact('hardware','kits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Usamos la sintaxis del motor de BD con el nombre explícito del esquema
            'sku' => [
                'required',
                'string',
                'max:50',
                // Asegúrate de escribirlo tal cual:
                Rule::unique('pgsql.commerce.products', 'sku')
            ],
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kits' => 'nullable|array', // Validamos que los kits vengan en un arreglo
            'kits.*' => 'exists:pgsql.commerce.kits,id' // Validamos que los ID existan realmente
        ], [
            'sku.unique' => 'Este SKU ya está registrado en el sistema.',
            'image.image' => 'El archivo debe ser una imagen válida.'
        ]);

        // 2. Procesar la subida de la imagen (si existe)
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Guarda la imagen en la carpeta 'hardware' dentro del disco 'public'
            $imagePath = $request->file('image')->store('hardware', 'public');
        }

        // 3. Crear el registro en la base de datos (Modelo Product)
        $product = Product::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'), // Checkbox de estado
        ]);

        // Guardar la relación en la tabla kit_product (si seleccionó alguno)
        if ($request->has('kits')) {
            // Sincronizamos los kits seleccionados
            $product->kits()->attach($request->input('kits'));
        }

        // 4. Redirigir de vuelta con mensaje de éxito
        return redirect()->route('admin.catalog.hardware')
            ->with('success', 'Hardware registrado exitosamente.');
    }


    public function update(Request $request, $id)
    {
        $hardware = Product::findOrFail($id);

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                // Ignoramos el ID actual para que no dé error de SKU duplicado consigo mismo
                Rule::unique('pgsql.commerce.products', 'sku')->ignore($hardware->id)
            ],
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'kits' => 'nullable|array',
            'kits.*' => 'exists:pgsql.commerce.kits,id'
        ], [
            'sku.unique' => 'Este SKU ya está registrado en otro dispositivo.',
            'image.image' => 'El archivo debe ser una imagen válida.'
        ]);

        $imagePath = $hardware->image_path;
        if ($request->hasFile('image')) {
            if ($hardware->image_path) {
                Storage::disk('public')->delete($hardware->image_path);
            }
            $imagePath = $request->file('image')->store('hardware', 'public');
        }

        $hardware->update([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        // Sincronizamos los kits: esto agrega los nuevos y elimina los desmarcados automáticamente
        $hardware->kits()->sync($request->input('kits', []));

        return redirect()->route('admin.catalog.hardware')
            ->with('success', 'Hardware actualizado exitosamente.');
    }
}
