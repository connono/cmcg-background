<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'path'];

    public function equipment()
    {
        return $this->belongsTo(EquipmentApplyRecord::class);
    }
}
