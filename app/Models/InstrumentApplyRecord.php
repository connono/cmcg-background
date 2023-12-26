<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvanceRecord;

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
        'install_date',
        'status',
        'apply_picture',
        'survey_picture',
        'purchase_picture',
        'install_picture',
        'isAdvance',        // 是否垫付
        'advance_status',   // 垫付状态
        'advance_record_id',
    ];

    public function advanceRecord(): BelongsTo
    {
        return $this->belongsTo(AdvanceRecord::class);
    }
}
