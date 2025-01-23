<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\RequestForMaterial;

use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pengajuanTerakhir = collect();
        $stockAvailable = collect();

        if (auth()->user()->is_admin) {
            $bahan = Material::count();
            $kategori = Category::count();
            $totalPengajuan = RequestForMaterial::count();
            $stockUnderLimit = Material::where('stock', '<', 10)->get();

            $widget = [
                'bahan' => $bahan,
                'kategori'  => $kategori,
                'totalPengajuan' => $totalPengajuan,
                'stockUnderLimit' => $stockUnderLimit->count()
            ];


            $pengajuanTerakhir = RequestForMaterial::latest()->limit(10)->get();
            $getMonthlyRequest = RequestForMaterial::where('status', 'approved')
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->get();

            $lastMonth = Carbon::now()->subMonth();

            $materials = Material::with('category')
                ->withSum(['requestForMaterials as used_quantity' => function ($query) use ($lastMonth) {
                    $query->where('status', 'approved')
                        ->where('created_at', '>=', $lastMonth);
                }], 'quantity')
                ->latest()
                ->get()
                ->map(function ($material) use ($lastMonth) {
                    $material->total_used_value = $material->getTotalUsedValueLastMonth();
                    return $material;
                });


            return view('home', compact('widget', 'stockUnderLimit', 'pengajuanTerakhir', 'materials'));
        }

        // For non-admin users, show their pengajuan
        $pengajuanTerakhir = RequestForMaterial::where('user_id', auth()->id())->latest()->limit(5)->get();
        $stockAvailable = Material::where('stock', '>', 0)->get();

        return view('home', compact('pengajuanTerakhir', 'stockAvailable'));
    }
}
