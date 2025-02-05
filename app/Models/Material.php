<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Material extends Model
{
    protected $fillable = [
        'material_id',
        'name',
        'description',
        'category_id',
        'price',
        'stock',
        'unit'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function generateMaterialId()
    {
        $prefix = 'BHN';
        $lastMaterial = self::orderBy('id', 'desc')->first();

        if ($lastMaterial) {
            $lastNumber = intval(substr($lastMaterial->material_id, 4));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . '-' . $newNumber;
    }

    public function getTotalUsedValueLastMonth()
    {
        $lastMonth = Carbon::now()->subMonth();

        $totalUsed = $this->requestForMaterials()
            ->where('status', 'approved')
            ->where('created_at', '>=', $lastMonth)
            ->sum('quantity');

        return $totalUsed * $this->price;
    }

    public function requestForMaterials()
    {
        return $this->hasMany(RequestForMaterial::class);
    }

    public function purchaseMaterials()
    {
        return $this->hasMany(PurchaseMaterial::class, 'name', 'name');
    }
}
