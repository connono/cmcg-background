<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PaymentPlan;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'permission',
        'title',
        'body',
        'link',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongs(PaymentPlan::class);
    }
}
