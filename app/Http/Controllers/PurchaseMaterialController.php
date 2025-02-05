<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\PurchaseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PurchaseMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Pastikan pengguna sudah login
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Query dasar
        $query = PurchaseMaterial::with(['user', 'category']);

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Jika bukan admin, filter berdasarkan user_id
        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        // Filter pencarian berdasarkan nama user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Ambil data dengan pagination
        $purchases = $query->latest()->paginate(10);

        // Pastikan view ada sebelum dikembalikan
        if (!view()->exists('layouts.admin.permintaan.index')) {
            abort(404, 'Halaman tidak ditemukan');
        }

        return view('layouts.admin.permintaan.index', compact('purchases'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('layouts.admin.permintaan.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer|min:1',
            'description' => 'required',
            'unit' => 'required',
            'category_id' => 'required|exists:categories,id'

        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        PurchaseMaterial::create($validated);

        return redirect()->route('purchase-materials.index')->with('success', 'Purchase Material created successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseMaterial  $purchaseMaterial
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseMaterial $purchaseMaterial)
    {
        if (!Auth::user()->is_admin && Auth::id() !== $purchaseMaterial->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $purchaseMaterial->delete();

        return redirect()->route('purchase-materials.index')->with('success', 'Purchase Material deleted successfully');
    }

    public function approve(PurchaseMaterial $purchaseMaterial)
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseMaterial->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be approved.');
        }

        DB::beginTransaction();
        try {
            // Update purchase material status
            $purchaseMaterial->update(['status' => 'approved']);

            // Find or create material
            $material = Material::where('name', $purchaseMaterial->name)->first();

            if (!$material) {
                // Create new material if it doesn't exist
                $material = Material::create([
                    'material_id' => Material::generateMaterialId(),
                    'name' => $purchaseMaterial->name,
                    'stock' => $purchaseMaterial->quantity,
                    'unit' => $purchaseMaterial->unit,
                    'category_id' => 1,
                ]);
            } else {
                // Update existing material stock
                $material->update([
                    'stock' => $material->stock + $purchaseMaterial->quantity
                ]);
            }

            DB::commit();

            // WhatsApp notification code
            $whatsappNumber = preg_replace('/[^0-9]/', '', $purchaseMaterial->user->phone);
            $message = "Halo, {$purchaseMaterial->user->name},\n\n"
                . "Permintaan pembelian material Anda telah DISETUJUI.\n\n"
                . "Material: {$purchaseMaterial->name}\n"
                . "Kuantitas: {$purchaseMaterial->quantity} {$purchaseMaterial->unit}\n"
                . "Silakan lanjutkan pengumpulan material.";
            $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $whatsappNumber . "&text=" . urlencode($message);

            return redirect()->back()
                ->with([
                    'success' => 'Request has been approved and material stock has been updated.',
                    'whatsapp_url' => $whatsappUrl
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred while approving the request.');
        }
    }


    public function reject(Request $request, PurchaseMaterial $purchaseMaterial)
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'reject_reason' => 'required|string|max:255'
        ]);

        if ($purchaseMaterial->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be rejected.');
        }

        $purchaseMaterial->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason']
        ]);

        $whatsappNumber = preg_replace('/[^0-9]/', '', $purchaseMaterial->user->phone);
        $message = "Halo, {$purchaseMaterial->user->name},\n\n"
            . "Permintaan material Anda telah DITOLAK.\n\n"
            . "Material: {$purchaseMaterial->name}\n"
            . "Kuantitas: {$purchaseMaterial->quantity}  {$purchaseMaterial->unit}\n"
            . "Alasan penolakan: " . $validated['reject_reason'] . "\n\n"
            . "Silakan hubungi admin untuk informasi lebih lanjut.";
        $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $whatsappNumber . "&text=" . urlencode($message);

        return redirect()->back()
            ->with([
                'success' => 'Request has been rejected.',
                'whatsapp_url' => $whatsappUrl
            ]);
    }
}
