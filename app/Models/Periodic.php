<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Periodic extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'periodicity',
        'next_service_date',
        'can_alert'
    ];

    public function house(): BelongsTo {
        return $this->belongsTo(House::class);
    }
}