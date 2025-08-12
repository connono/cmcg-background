<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumableStateEvent extends Model
{
    protected $fillable = [
        'consumable_id',
        'event_type',
        'from_state',
        'to_state',
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