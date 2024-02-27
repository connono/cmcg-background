<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PaymentProcess;

class PaymentProcessRecord extends Model
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

    public function process(): BelongsTo
    {
        return $this->belongsTo(PaymentProcess::class);
    }
}
