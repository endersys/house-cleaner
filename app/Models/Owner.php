<?php

namespace App\Models;

use App\Enums\ClientStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_client',
        'status'
    ];

    protected $casts = [
        'status' => ClientStatusEnum::class
    ];

    public function houses() {
        return $this->hasMany(House::class);
    }
}
