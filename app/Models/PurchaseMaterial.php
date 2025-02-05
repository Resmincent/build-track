<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'description',
        'reject_reason',
        'quantity',
        'user_id',
        'unit',
        'category_id',
        'material_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
