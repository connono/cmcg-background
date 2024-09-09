<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class ConsumableApplyTable extends Model
{
    use HasFactory;
    protected $fillable = [
        'serial_number',           // 申请单号
        'platform_id',                   // 平台ID
        'department',                    // 申请科室
        'consumable',                    // 耗材名称
        'model',                         // 型号
        'price',                         // 采购价格
        'apply_date',                    // 申请日期
        'count_year',                    //年用量
        'registration_num',              // 注册证号
        'company',                       // 供应商
        'manufacturer',                  // 生产厂家
        'category_zj',                   // 浙江分类
        'parent_directory',              // 一级目录
        'child_directory',               // 二级目录
        'apply_type',                    // 采购类型
        'pre_assessment',                // 初评意见
        'final',                         // 终评结论
        'apply_file',                    // 申请表附件
        'in_drugstore',                  // 是否为便民药房
        'status',                        // 状态
       
    ];

    public function notification(): HasOne
    {
        return $this->hasOne(\App\Models\Notification::class);
    }
}
