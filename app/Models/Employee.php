<?php

namespace App\Models;

use App\Enums\EmployeeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status'
    ];

    protected $casts = [
        'status' => EmployeeStatusEnum::class
    ];

    public function services() {
        return $this->belongsToMany(Service::class);
    }
}
