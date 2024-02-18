<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\PaymentRecord;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_name',
        'department',
        'company',
        'payment_file',
        'next_date',
        'contract_date',
        'assessment',
        'finish_date',
        'is_pay',
        'category',
        'records_count',
        'assessments_count',
        'status',
        'current_payment_record_id',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function notification(): HasOne
    {
        return $this->hasOne(\App\Models\Notification::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_plan_id');
    }
}
