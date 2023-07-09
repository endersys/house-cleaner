<?php

namespace App\Models;

use App\Enums\ServiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'service_date',
        'status',
        'price',
        'type',
        'started_at',
        'finished_at',
        'notes'
    ];

    protected $casts = [
        'status' => ServiceStatusEnum::class
    ];

    public function house() {
        return $this->belongsTo(House::class);
    }

    public function employees() {
        return $this->belongsToMany(Employee::class);
    }

    public function materials() {
        return $this->belongsToMany(Material::class);
    }
}
