<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'is_functional',
        'engineer_id',
        'leader_id',
        'chief_leader_id',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
