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
        'unit'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
