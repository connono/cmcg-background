<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ApprovalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 	//用户ID
        'approve_date', 	//审批日期
        'approve_model', 	//审批Model
        'approve_model_id', 	//审批ModelId
        'approve_status', 	//审批status
        'reject_reason',
    ];
}                  
