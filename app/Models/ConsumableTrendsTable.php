<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class ConsumableTrendsTable extends Model
{
    use HasFactory;
    protected $fillable = [
        'consumable_apply_id',           // 申请单号
        'platform_id',                   // 平台ID
        'department',                    // 申请科室
        'consumable',                    // 耗材名称
        'model',                         // 型号
        'price',                         // 采购价格
        'start_date',                    // 合同日期
        'exp_date',                      // 失效日期
        'registration_num',              // 注册证号
        'company',                       // 供应商
        'manufacturer',                  // 生产厂家
        'category_zj',                   // 浙江分类
        'parent_directory',              // 一级目录
        'child_directory',               // 二级目录
        'apply_type',                    // 采购类型
        'contact_file',                  // 合同附件
        'is_need',                       // 是否执行采购
        'reason',                        // 不执行采购理由
    ];

    public function notification(): HasOne
    {
        return $this->hasOne(\App\Models\Notification::class);
    }
}
