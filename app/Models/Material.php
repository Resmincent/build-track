<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock', 'unit', 'category_id'];

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
}
