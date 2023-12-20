<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'create_date',      // 制单日期
        'all_price',        // 总金额
        'status',           // 回款状态
        'payback_date',     // 回款日期
    ];

    public function equipmentApplyRecords(): HasMany
    {
        return $this->hasMany(\App\Models\EquipmentApplyRecord::class);
    }

    public function instrumentApplyRecords(): HasMany
    {
        return $this->hasMany(\App\Models\InstrumentApplyRecord::class);
    }

    public function repairApplyRecords(): HasMany
    {
        return $this->hasMany(\App\Models\RepairApplyRecord::class);
    }
}
