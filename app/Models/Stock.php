<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'quantity'
    ];

    public function material() {
        return $this->belongsTo(Material::class);
    }
}
