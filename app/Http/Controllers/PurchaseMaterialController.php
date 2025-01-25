<?php

namespace App\Http\Controllers;

use App\Models\PurchaseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $user = Auth::user();
        $query = PurchaseMaterial::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $purchases = $query->latest()->paginate(10);

        return view('layouts.admin.permintaan.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.admin.permintaan.create');
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

        $purchaseMaterial->update(['status' => 'approved']);

        $whatsappNumber = preg_replace('/[^0-9]/', '', $purchaseMaterial->user->phone);
        $message = "Halo, {$purchaseMaterial->user->name},\n\n"
            . "Permintaan pembelian material Anda telah DISETUJUI.\n\n"
            . "Material: {$purchaseMaterial->name}\n"
            . "Kuantitas: {$purchaseMaterial->quantity} {$purchaseMaterial->unit}\n"
            . "Silakan lanjutkan pengumpulan material.";
        $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $whatsappNumber . "&text=" . urlencode($message);

        return redirect()->back()
            ->with([
                'success' => 'Request has been approved.',
                'whatsapp_url' => $whatsappUrl
            ]);
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
