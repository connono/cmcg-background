<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_name',
        'series_number',
        'category',
        'contractor',
        'source',
        'price',
        'isImportant',
        'comment',
        'isComplement',
        'contract_file',
        'contract_docx',
    ];

    public function manager (): HasOne
    {
        return $this->hasOne(User::class, 'manager_id');
    }

    public function manage_dean (): HasOne
    {
        return $this->hasOne(User::class, 'manage_dean_id');
    }

    public function dean (): HasOne
    {
        return $this->hasOne(User::class, 'dean_id');
    }

    public function plans (): HasMany
    {
        return $this->hasMany(PaymentPlan::class);
    }

    public function processes (): HasMany
    {
        return $this->hasMany(PaymentProcess::class);
    }
}
