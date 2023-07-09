<?php

namespace App\Models;

use App\Enums\HouseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'number',
        'street',
        'postal_code',
        'district',
        'city',
        'state',
        'country',
        'status'
    ];

    protected $casts = [
        'status' => HouseStatusEnum::class
    ];

    public function owner() {
        return $this->belongsTo(Owner::class);
    }

    public function periodicity() {
        return $this->hasOne(Periodic::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }
}
