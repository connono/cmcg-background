<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairApplyRecord extends Model
{
    use HasFactory;

    protected $table = 'repair_records';

    protected $fillable = [
        'serial_number',
        'status',
        'name',
        'equipment',
        'department',
        'budget',
        'apply_date',
        'price',
        'install_file',
        'isAdvance',
    ];
}
