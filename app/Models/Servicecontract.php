<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicecontract extends Model
{
    use HasFactory;

    protected $casts = [
        'can_expire' => 'boolean'
    ];
}
