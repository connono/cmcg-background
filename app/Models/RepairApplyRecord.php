<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvanceRecord;

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
        'isAdvance',        // 是否垫付
        'advance_status',   // 垫付状态
    ];

    public function advanceRecord(): BelongsTo
    {
        return $this->belongsTo(AdvanceRecord::class);
    }
}
