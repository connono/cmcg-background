<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'create_date',
        'all_price',
        'status',
        'excel_url',
        'user_id_1',
        'user_id_2',
        'user_id_3',
        'user_id_4',
        'user_id_5',
    ];

    public function paymentProcessRecords(): HasMany
    {
        return $this->hasMany(\App\Models\PaymentProcessRecord::class);
    }
}
