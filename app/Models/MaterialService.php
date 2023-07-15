<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialService extends Model
{
    use HasFactory;

    protected $table = 'material_service';

    protected $fillable = [
        'material_id',
        'service_id',
        'quantity'
    ];

    public function material() {
        return $this->belongsTo(Material::class);
    }
}