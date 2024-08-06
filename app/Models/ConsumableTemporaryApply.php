<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableTemporaryApply extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',           // 状态
        'serial_number',    // 申请单号
        // 申请
        'department',       // 申请科室
        'consumable',       // 耗材名称
        'count',            // 数量
        'budget',           // 预算
        'model',            // 型号
        'manufacturer',     // 生产厂家
        'telephone',        //联系方式
        'registration_num', // 注册证号
        'reason',           // 申请理由
        'apply_date',       // 申请日期
        'apply_type',       // 采购类型
        'apply_file',       // 申请单附件
        // 采购后录入
        'product_id',       // 平台产品ID
        'arrive_date',      // 采购日期
        'arrive_price',     // 采购单价
        'company',          // 供应商
        'telephone2',       // 供应商电话
        'accept_file',      // 验收单附件

        
        // 中止
       // 'is_stop',          //是否终止
        'stop_reason',      //终止原因
    ];
}
