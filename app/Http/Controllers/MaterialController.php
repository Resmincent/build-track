<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Category;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('category')->latest()->get();
        $categories = Category::all();
        return view('layouts.admin.material.index', compact('materials', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'stock' => 'required|integer',
            'unit' => 'required|max:100',
            'category_id' => 'required|exists:categories,id'
        ]);

        // Generate material ID
        $materialId = Material::generateMaterialId();

        // Create material with generated ID
        $material = new Material($request->all());
        $material->material_id = $materialId;
        $material->save();

        return redirect()->route('materials.index')
            ->with('success', 'Bahan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'stock' => 'required|integer',
            'unit' => 'required|max:100',
            'category_id' => 'required|exists:categories,id'
        ]);

        $material->update($request->all());

        return redirect()->route('materials.index')
            ->with('success', 'Bahan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Bahan berhasil dihapus');
    }
}
