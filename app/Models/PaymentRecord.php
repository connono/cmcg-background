<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PaymentPlan;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_name',
        'department',
        'company',
        'assessment',
        'payment_voucher_file',
        'assessment_date',
        'payment_file',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }
}
