<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function servicecontracts() {
        return $this->belongsToMany(Servicecontract::class)->withPivot(['expire_at']);
    }

    public function workreports() {
        return $this->belongsToMany(Workreport::class);
    }
}
