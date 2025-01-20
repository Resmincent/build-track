<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestForMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_id',
        'status',
        'quantity',
        'date_needed',
        'reject_reason',
    ];

    protected $casts = [
        'date_needed' => 'date'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
