<?php

namespace App\Models;

use App\Enums\MaterialStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'reference',
        'measurement_unit',
        'expiration_date',
        'shelf',
        'notes',
        'status'
    ];

    protected $casts = [
        'status' => MaterialStatusEnum::class
    ];

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function suppliers() {
        return $this->belongsToMany(Supplier::class);
    }

    public function stock() {
        return $this->hasOne(Stock::class);
    }

    public function services() {
        return $this->belongsToMany(Service::class);
    }
}
