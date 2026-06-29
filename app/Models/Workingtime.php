<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workingtime extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'datetime',
        // 'travel_time_from' => TimeCast::class,
        // 'travel_time_to' => TimeCast::class,
        // 'work_from' => TimeCast::class,
        // 'work_to' => TimeCast::class,
    ];
}
