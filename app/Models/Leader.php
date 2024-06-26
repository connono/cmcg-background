<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(\App\Models\User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(\App\Models\Department::class);
    }

    public function chief_departments(): HasMany
    {
        return $this->hasMany(\App\Models\Department::class, 'chief_leader_id');
    }
}
