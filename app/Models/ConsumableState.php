<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumableState extends Model
{
    protected $fillable = [
        'consumable_id',
        'state',
        'attributes',
        'metadata',
    ];

    protected $casts = [
        'attributes' => 'array',
        'metadata' => 'array',
    ];

    public function consumable(): BelongsTo
    {
        return $this->belongsTo(Consumable::class);
    }
} 