<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Carbon\Carbon;

class MonthlyStockReportController extends Controller
{
    public function index(Request $request)
    {
        // Get selected month or default to current month
        $selectedDate = $request->input('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))
            : Carbon::now();

        $materials = Material::with('category')
            ->withSum(['requestForMaterials as used_quantity' => function ($query) use ($selectedDate) {
                $query->where('status', 'approved')
                    ->whereBetween('created_at', [
                        $selectedDate->copy()->startOfMonth(),
                        $selectedDate->copy()->endOfMonth()
                    ]);
            }], 'quantity')
            ->get()
            ->map(function ($material) use ($selectedDate) {
                $totalUsed = $material->requestForMaterials()
                    ->where('status', 'approved')
                    ->whereBetween('created_at', [
                        $selectedDate->copy()->startOfMonth(),
                        $selectedDate->copy()->endOfMonth()
                    ])
                    ->sum('quantity');

                $material->total_used_value = $totalUsed * $material->price;

                return $material;
            });

        $summary = [
            'total_items' => $materials->count(),
            'total_value_used' => $materials->sum('total_used_value'),
            'month_name' => $selectedDate->format('F Y')
        ];

        // Get list of available months for dropdown (last 12 months)
        $availableMonths = collect(range(0, 11))->map(function ($i) {
            $date = Carbon::now()->subMonths($i);
            return [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y')
            ];
        });

        return view('layouts.admin.laporan.index', compact(
            'materials',
            'summary',
            'availableMonths',
            'selectedDate'
        ));
    }
}
