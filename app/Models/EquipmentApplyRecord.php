<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EquipmentApplyRecord extends Model
{
    use HasFactory;
        
    protected $fillable = [
        'status',           // 状态
        'serial_number',    // 申请编号
        // 申请
        'equipment',        // 设备名称
        'department',       // 申请科室
        'count',            // 数量
        'budget',           // 预算
        'apply_type',       // 申请方式
        'apply_picture',    // 申请图片
        // 调研
        'survey_date',      // 调研日期
        'purchase_type',    // 收购方式
        'survey_record',    // 调研记录
        'meeting_record',   // 会议记录
        'survey_picture',   // 调研图片
        // 政府审批
        'approve_date',     // 审批日期
        'execute_date',     // 预算执行单日期
        'approve_picture',  // 审批图片
        // 招标
        'tender_date',      // 招标书日期
        'tender_file',      // 招标书附件
        'tender_boardcast_file',// 招标公告附件
        'tender_out_date',  // 招标日期
        'bid_winning_file', // 中标通知书
        'send_tender_file', // 投标文件
        // 合同
        'purchase_date',    // 合同日期
        'arrive_date',      // 到货日期
        'price',            // 合同价格
        'purchase_picture', // 合同图片
        // 安装验收
        'install_date',     // 安装日期
        'install_picture',   // 安装图片
        'isAdvance',        // 是否垫付
        'advance_status',   // 垫付状态
        'advance_record_id',
        // 入库
        'warehousing_date', // 入库日期
        // 中止
        'is_stop',          //是否终止
        'stop_reason',      //终止原因
    ];

    public function advanceRecord(): BelongsTo
    {
        return $this->belongsTo(AdvanceRecord::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function notification(): HasOne
    {
        return $this->hasOne(\App\Models\Notification::class);
    }
}
