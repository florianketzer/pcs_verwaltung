<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workreport extends Model
{
    use HasFactory;

    protected $casts = [
        'locked' => 'boolean',
        'date' => 'datetime'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function editor() {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function notusedmaterials() {
        return $this->hasMany(NotUsedMaterial::class);
    }

    public function additionalmaterials() {
        return $this->hasMany(AdditionalMaterial::class);
    }

    public function workingtimes() {
        return $this->hasMany(Workingtime::class);
    }

    public function documents() {
        return $this->belongsToMany(Document::class);
    }
}
