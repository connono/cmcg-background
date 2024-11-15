<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableNet extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumable_net_id',           // 申请单号
        'category',                   // 平台ID
        'parent_directory',                    // 申请科室
        'child_directory',                    // 耗材名称
        'product_id',                         // 型号
        'consumable',                         // 采购价格
        'registration_num',                    // 申请日期
        'registration_name',                    //年用量
        'registration_date',              // 注册证号
        'consumable_encoding',                       // 供应商
        'specification',                  // 生产厂家
        'model',                   // 浙江分类
        'units',              // 一级目录
        'company',               // 二级目录
        'company_encoding',                    // 采购类型
        'price',                // 初评意见
        'tempory_price',                         // 终评结论
        'source_name',                    // 申请表附件
        'product_remark',                  // 是否为便民药房
        'net_date',                        // 状态
        'purchase_category',                        // 状态
        'net_status',                        // 状态
        'withdrawal_time',                        // 状态
    ];
}
