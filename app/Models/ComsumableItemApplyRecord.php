<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComsumableItemApplyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        "department",
        "name",
        "spectification",
        "production_id",
        "price",
        "registration_number",
        "category_ZJ",
        "parent_directory",
        "child_directory",
    ] ;
}
