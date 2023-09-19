<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentApplyRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'serial_number',
        'instrument',
        'department',
        'count',
        'budget',

        'survey_date',

        'price',
        
        'install_date'
    ];


}
