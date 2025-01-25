<?php

namespace App\Http\Controllers;

use App\Models\RequestForMaterial;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestForMaterialController extends Controller
{
    /**
     * Display a listing of the requests based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = RequestForMaterial::with(['user', 'material']);

        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Check if user is admin
        if (!$user->is_admin) {
            // If not admin, only show their own requests
            $query->where('user_id', $user->id);
        }

        // Apply search if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('material', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Get the requests with pagination
        $requests = $query->latest()->paginate(10);

        return view('layouts.admin.pengajuan.index', compact('requests'));
    }
    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $materials = Material::select('id', 'name', 'price', 'unit', 'stock')->get();
        return view('layouts.admin.pengajuan.create', compact('materials'));
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer|min:1',
            'date_needed' => 'required|date|after:today',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        RequestForMaterial::create($validated);

        return redirect()->route('request-for-materials.index')
            ->with('success', 'Material request created successfully.');
    }

    /**
     * Remove the specified request from storage.
     */
    public function destroy(RequestForMaterial $requestForMaterial)
    {
        // Add authorization check
        if (!Auth::user()->is_admin && Auth::id() !== $requestForMaterial->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $requestForMaterial->delete();

        return redirect()->route('request-for-materials.index')
            ->with('success', 'Request deleted successfully.');
    }


    public function approve(RequestForMaterial $requestForMaterial)
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($requestForMaterial->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be approved.');
        }

        $requestForMaterial->update(['status' => 'approved']);

        $whatsappNumber = preg_replace('/[^0-9]/', '', $requestForMaterial->user->phone);
        $message = "Halo, {$requestForMaterial->user->name},\n\n"
            . "Permintaan material Anda telah DISETUJUI.\n\n"
            . "Material: {$requestForMaterial->material->name}\n"
            . "Kuantitas: {$requestForMaterial->quantity}\n"
            . "Tanggal Diperlukan: " . $requestForMaterial->date_needed->format('d/m/Y') . "\n\n"
            . "Silakan lanjutkan pengumpulan material.";
        $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $whatsappNumber . "&text=" . urlencode($message);

        // Store the WhatsApp URL in a separate flash variable
        return redirect()->back()
            ->with([
                'success' => 'Request has been approved.',
                'whatsapp_url' => $whatsappUrl
            ]);
    }
    public function reject(Request $request, RequestForMaterial $requestForMaterial)
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'reject_reason' => 'required|string|max:255'
        ]);

        if ($requestForMaterial->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending requests can be rejected.');
        }

        $requestForMaterial->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason']
        ]);

        $whatsappNumber = preg_replace('/[^0-9]/', '', $requestForMaterial->user->phone);
        $message = "Halo, {$requestForMaterial->user->name},\n\n"
            . "Permintaan material Anda telah DITOLAK.\n\n"
            . "Material: {$requestForMaterial->material->name}\n"
            . "Kuantitas: {$requestForMaterial->quantity}\n"
            . "Tanggal Diperlukan: " . $requestForMaterial->date_needed->format('d/m/Y') . "\n"
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
