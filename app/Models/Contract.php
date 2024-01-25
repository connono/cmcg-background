<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'complementation_agreements',
        'contract_file',
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
}
