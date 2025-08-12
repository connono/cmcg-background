<?php

namespace App\Services;

use App\Models\Consumable;
use App\Models\ConsumableState;
use App\Models\ConsumableStateEvent;
use Illuminate\Support\Facades\DB;

class ConsumableService
{
    // 快照类型映射
    const SNAPSHOT_TYPES = [
        'applied' => 'snapshot:application',
        'selection_input' => 'snapshot:selection',
        'usage' => 'snapshot:usage'
    ];
    
    const APPROVAL_COMPLETION_STATES = [
        'applied' => 'applied_finish',          // 申请审核完成
        'medical_engineering_review' => 'sunshine_president_review', // 医工科审核完成
        'sunshine_president_review' => 'selection_finish'       // 分管院长审核完成
    ];

    /**
     * 创建新的耗材申请
     */
    public function createConsumable(array $data): array
    {
        try {
            DB::beginTransaction();

            // 创建耗材记录
            $consumable = Consumable::create([
                'platform_id' => $data['platform_id'],
                'department' => $data['department'],
                'consumable' => $data['consumable'],
                'model' => $data['model'],
                'price' => $data['price'],
                'manufacturer' => $data['manufacturer'],
                'in_drugstore' => $data['in_drugstore'] ?? false,
                'apply_type' => $data['apply_type'],
                'need_selection' => $data['need_selection'] ?? false,
                'apply_date' => $data['apply_date'] ?? null,
                'count_year' => $data['count_year'] ?? 0,
                'medical_approval_file' => $data['medical_approval_file'],
                'company' => $data['company'],
                'category_zj' => $data['category_zj'],
                'parent_directory' => $data['parent_directory'],
                'child_directory' => $data['child_directory'],
                'registration_num' => $data['registration_num'],
            ]);

            // 创建初始状态记录（保留原有结构）
            $state = ConsumableState::create([
                'consumable_id' => $consumable->id,
                'state' => 'applied',
                'attributes' => [
                    'initial_review_comment' => null,
                    'final_review_comment' => null,
                    'medical_approval_file' => $data['medical_approval_file'],
                    'registration_num' => $data['registration_num'],
                ],
                'metadata' => [
                    'created_by' => '0',
                    'created_at' => now(),
                ],
            ]);

            // ✅ 创建申请阶段快照
            $this->createSnapshot(
                $consumable,
                'initial', 
                'applied', 
                'application'
            );

            DB::commit();
            return ['consumable' => $consumable, 'state' => $state];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 更新耗材状态
     */
    public function updateConsumableState(
        Consumable $consumable, 
        string $newState, 
        array $attributes = []
    ) {
        return DB::transaction(function () use ($consumable, $newState, $attributes) {
            $currentState = $consumable->currentState?->state ?? null;

            // 更新耗材属性
            if (!empty($attributes)) {
                $consumable->update($attributes);
            }

            // 创建新状态记录
            $state = ConsumableState::create([
                'consumable_id' => $consumable->id,
                'state' => $newState,
                'attributes' => $attributes,
                'metadata' => [
                    'created_by' => '0',
                    'created_at' => now(),
                ],
            ]);

            // 创建状态事件
            ConsumableStateEvent::create([
                'consumable_id' => $consumable->id,
                'event_type' => 'state_change',
                'from_state' => $currentState,
                'to_state' => $newState,
                'attributes' => $attributes,
                'metadata' => [
                    'created_by' => '0',
                    'created_at' => now(),
                ],
            ]);

            // 确定当前阶段和快照类型
            $snapshotType = null;
            $isNewPhase = false;
            
            // 申请阶段
            if (in_array($newState, ['applied', 'applied_finish'])) {
                $snapshotType = 'snapshot:application';
            }
            // 遴选阶段
            elseif (in_array($newState, ['selection_input', 'medical_engineering_review', 'sunshine_president_review', 'selection_finish'])) {
                $snapshotType = 'snapshot:selection';
                // 如果是新进入遴选阶段
                if ($currentState === 'applied_finish') {
                    $isNewPhase = true;
                }
            }
            // 使用阶段
            elseif (in_array($newState, ['in_use'])) {
                $snapshotType = 'snapshot:usage';
                // 如果是新进入使用阶段
                if ($currentState === 'selection_finish') {
                    $isNewPhase = true;
                }
            }

            // 如果确定了快照类型
            if ($snapshotType) {
                // 查找或创建快照
                $snapshot = ConsumableStateEvent::where([
                    'consumable_id' => $consumable->id,
                    'event_type' => $snapshotType
                ])->first();

                if ($snapshot) {
                    // 检查是否是当前阶段的快照
                    $isCurrentPhaseSnapshot = false;
                    
                    // 申请阶段
                    if ($snapshotType === 'snapshot:application' && in_array($newState, ['applied', 'applied_finish'])) {
                        $isCurrentPhaseSnapshot = true;
                    }
                    // 遴选阶段
                    elseif ($snapshotType === 'snapshot:selection' && in_array($newState, ['selection_input', 'medical_engineering_review', 'sunshine_president_review', 'selection_finish'])) {
                        $isCurrentPhaseSnapshot = true;
                    }
                    // 使用阶段
                    elseif ($snapshotType === 'snapshot:usage' && $newState === 'in_use') {
                        $isCurrentPhaseSnapshot = true;
                    }

                    if ($isCurrentPhaseSnapshot && !$isNewPhase) {
                        // 如果是当前阶段的快照且不是新阶段开始，更新状态和属性
                        $snapshot->update([
                            'to_state' => $newState,
                            'attributes' => $consumable->fresh()->toArray(),
                            'metadata' => array_merge($snapshot->metadata ?? [], [
                                'approved_by' => '0',
                                'approved_at' => now()
                            ])
                        ]);
                    } else {
                        // 如果不是当前阶段的快照或是新阶段开始，只更新状态
                        $snapshot->update([
                            'to_state' => $newState,
                            'metadata' => array_merge($snapshot->metadata ?? [], [
                                'approved_by' => '0',
                                'approved_at' => now()
                            ])
                        ]);
                    }
                } else {
                    // 创建新快照
                    $this->createSnapshot(
                        $consumable,
                        $currentState,
                        $newState
                    );
                }
            }

            // 处理自动状态转换
            $nextState = null;

            // 规则1: applied_finish -> selection_input
            if ($newState === 'applied_finish') {
                $nextState = 'selection_input';
            }
            // 规则2: selection_finish -> in_use
            elseif ($newState === 'selection_finish') {
                $nextState = 'in_use';
            }

            // 如果存在下一个状态，递归调用更新状态
            if ($nextState) {
                // 确保返回最新的状态
                $consumable = $consumable->fresh(['currentState']);
                
                // 递归调用更新下一个状态
                return $this->updateConsumableState($consumable, $nextState, $attributes);
            }

            // 确保返回最新的状态
            $consumable = $consumable->fresh(['currentState']);

            return [
                'consumable' => $consumable,
                'state' => $state
            ];
        });
    }

    /**
     * 创建快照
     */
    private function createSnapshot(
        Consumable $consumable, 
        string $fromState, 
        string $toState
    ): ConsumableStateEvent {
        // 确保获取最新的属性
        $fullSnapshot = $consumable->fresh()->toArray();
        $fullSnapshot['state'] = $toState;

        // 确定快照类型
        $snapshotType = null;
        
        // 申请阶段
        if (in_array($toState, ['applied', 'applied_finish'])) {
            $snapshotType = 'snapshot:application';
        }
        // 遴选阶段
        elseif (in_array($toState, ['selection_input', 'medical_engineering_review', 'sunshine_president_review', 'selection_finish'])) {
            $snapshotType = 'snapshot:selection';
        }
        // 使用阶段
        elseif (in_array($toState, ['in_use'])) {
            $snapshotType = 'snapshot:usage';
        }

        return ConsumableStateEvent::create([
            'consumable_id' => $consumable->id,
            'event_type' => $snapshotType,
            'from_state' => $fromState,
            'to_state' => $toState,
            'attributes' => $fullSnapshot,
            'metadata' => ['created_by' => '0', 'created_at' => now()]
        ]);
    }

    /**
     * 判断是否是审核状态
     */
    private function isAuditState(string $state): bool
    {
        return in_array($state, [
            'department_leader_review',
            'medical_engineering_review',
            'sunshine_president_review',
            'access_leader_review'
        ]);
    }
} 