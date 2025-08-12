<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableSelectedNet extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumable',
        'model',
        'manufacturer',
        'registration_num',
        'company',
        'price',
        'product_id',
        'consumable_net_id',
        'category',
        'parent_directory',
        'child_directory',
    ];
}
