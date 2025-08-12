<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consumable extends Model
{
    protected $fillable = [
        'platform_id',
        'department',
        'consumable',
        'model',
        'price',
        'start_date',
        'exp_date',
        'registration_num',
        'company',
        'manufacturer',
        'category_zj',
        'parent_directory',
        'child_directory',
        'apply_type',
        'apply_date',
        'count_year',
        'in_drugstore',
        'need_selection',
        'medical_approval_file', // 新增医疗申请审批单字段
        'sunshine_purchase_file',
        'bid_purchase_file',
    ];

    protected $casts = [
        'start_date' => 'date',
        'exp_date' => 'date',
        'in_drugstore' => 'boolean',
        'need_selection' => 'boolean',
        'price' => 'decimal:2',
        'medical_approval_file' => 'string', // 新增类型转换
    ];

    // 获取当前状态
    public function currentState(): HasOne
    {
        return $this->hasOne(ConsumableState::class)
            ->latest('id')
            ->withDefault([
                'state' => 'applied',
                'attributes' => [],
                'metadata' => []
            ]);
    }

    // 获取所有状态历史
    public function stateHistory(): HasMany
    {
        return $this->hasMany(ConsumableState::class);
    }

    // 获取所有状态事件
    public function stateEvents(): HasMany
    {
        return $this->hasMany(ConsumableStateEvent::class);
    }

    // 更新状态
    public function updateState(string $newState, array $attributes = [], array $metadata = []): void
    {
        $currentState = $this->currentState;
        
        // 记录状态事件
        $this->stateEvents()->create([
            'event_type' => 'state_change',
            'from_state' => $currentState ? $currentState->state : null,
            'to_state' => $newState,
            'attributes' => $attributes,
            'metadata' => $metadata,
        ]);

        // 更新当前状态
        $this->currentState()->create([
            'state' => $newState,
            'attributes' => $attributes,
            'metadata' => $metadata,
        ]);

        // 如果状态是deleted，则删除记录
        if ($newState === 'deleted') {
            $this->delete();
        }
    }

    // 检查是否可以进入遴选状态
    public function canEnterSelection(): bool
    {
        return $this->need_selection && $this->currentState?->state === 'department_leader_review';
    }

    // 检查是否可以进入使用状态
    public function canEnterUsage(): bool
    {
        $currentState = $this->currentState?->state;
        // 仅允许从最终审核节点进入usage
        return in_array($currentState, ['sunshine_president_review', 'access_leader_review']);
    }

    // 检查是否需要上传文件
    public function needsFileUpload(): bool
    {
        $currentState = $this->currentState?->state;
        if ($currentState !== 'selection_input') {
            return false;
        }

        if ($this->apply_type === '阳光采购') {
            return empty($this->sunshine_purchase_file);
        } elseif ($this->apply_type === '中标采购') {
            return empty($this->bid_purchase_file);
        }

        return false;
    }
}