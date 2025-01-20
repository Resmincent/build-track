<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\RequestForMaterial;
use App\Models\User;
use Illuminate\Http\Request;

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
            $totalStock = Material::sum('stock');
            $kategori = Category::count();
            $totalPengajuan = RequestForMaterial::count();
            $stockUnderLimit = Material::where('stock', '<', 10)->get();

            $widget = [
                'bahan' => $bahan,
                'stokBahan' => $totalStock,
                'kategori'  => $kategori,
                'totalPengajuan' => $totalPengajuan,
                'stockUnderLimit' => $stockUnderLimit->count()
            ];

            // Get the last 5 pengajuan for the admin's dashboard
            $pengajuanTerakhir = RequestForMaterial::latest()->limit(5)->get();

            return view('home', compact('widget', 'stockUnderLimit', 'pengajuanTerakhir'));
        }

        // For non-admin users, show their pengajuan
        $pengajuanTerakhir = RequestForMaterial::where('user_id', auth()->id())->latest()->limit(5)->get();
        $stockAvailable = Material::where('stock', '>', 0)->get();

        return view('home', compact('pengajuanTerakhir', 'stockAvailable'));
    }
}
