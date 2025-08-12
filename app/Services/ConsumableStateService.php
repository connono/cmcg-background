<?php

class ConsumableStateService
{
    // 状态转换规则
    private array $stateTransitions = [
        'applied' => ['department_leader_review', 'applied_finish'], // ✅ 添加 applied_finish
        'department_leader_review' => ['applied_finish', 'deleted'],
        
        'applied_finish' => ['selection'],  // 移除直接到usage的路径
        
        'selection' => ['selection_input'],  
        
        'selection_input' => ['sunshine_selection_path', 'bid_selection_path'],  

        // 阳光采购完整路径
        'sunshine_selection_path' => ['medical_engineering_review'],  
        'medical_engineering_review' => ['sunshine_president_review', 'selection_input'],  // 新增分管院长审核节点
        'sunshine_president_review' => ['usage', 'selection_input'],  // 阳光采购最终审核

        // 中标采购路径
        'bid_selection_path' => ['access_leader_review'],  
        'access_leader_review' => ['usage', 'selection_input'],  

        'usage' => ['archived'],  
        'deleted' => []  
    ];

    // 检查状态转换是否有效
    public function canTransitionTo(Consumable $consumable, string $newState): bool
    {
        $currentState = $consumable->currentState?->state ?? 'applied';
        return in_array($newState, $this->stateTransitions[$currentState] ?? []);
    }

    // 获取下一个可能的状态
    public function getNextPossibleStates(Consumable $consumable): array
    {
        $currentState = $consumable->currentState?->state ?? 'applied';
        return $this->stateTransitions[$currentState] ?? [];
    }

    // 根据属性确定下一个状态
    public function determineNextState(Consumable $consumable, array $attributes): string
    {
        $currentState = $consumable->currentState?->state ?? 'applied';
        
        switch ($currentState) {
            case 'applied':
                return $this->handleAppliedState($consumable, $attributes);
            case 'department_leader_review':
                return $this->handleDepartmentLeaderReviewState($consumable, $attributes);
            case 'selection':
                return $this->handleSelectionState($consumable, $attributes);
            case 'selection_input':
                return $this->handleSelectionInputState($consumable, $attributes);
            case 'medical_engineering_review':
                return $this->handleMedicalEngineeringReviewState($consumable, $attributes);
            case 'access_leader_review':
                return $this->handleAccessLeaderReviewState($consumable, $attributes);
            case 'sunshine_president_review':
                return $this->handleSunshinePresidentReviewState($consumable, $attributes);
            default:
                return $currentState;
        }
    }

    // 处理申请状态
    private function handleAppliedState(Consumable $consumable, array $attributes): string
    {
        // 检查必要字段是否填写完整
        if ($this->isApplicationComplete($attributes)) {
            return 'department_leader_review';
        }
        return 'applied';
    }

    // 处理分管领导审核状态
    private function handleDepartmentLeaderReviewState(Consumable $consumable, array $attributes): string
    {
        if (!isset($attributes['is_approved'])) {
            return 'department_leader_review';
        }
    
        if (!$attributes['is_approved']) {
            return 'deleted';
        }
    
        // 根据是否需要遴选决定路径
        return $attributes['need_selection'] ? 'applied_finish' : 'usage';
    }

    private function handleAppliedFinishState(Consumable $consumable, array $attributes): string
    {
        // 自动流转到遴选决策状态
        return 'selection';
    }

    // 处理遴选状态
    private function handleSelectionState(Consumable $consumable, array $attributes): string
    {
        return 'selection_input';
    }

    // 处理遴选录入状态
    private function handleSelectionInputState(Consumable $consumable, array $attributes): string
    {
        // 根据采购类型决定路径
        return $attributes['apply_type'] === '阳光采购' 
            ? 'sunshine_selection_path' 
            : 'bid_selection_path';
    }

    // 处理医工科审核状态
    private function handleMedicalEngineeringReviewState(Consumable $consumable, array $attributes): string
    {
        if (!isset($attributes['is_approved'])) {
            return 'medical_engineering_review';
        }

        if (!$attributes['is_approved']) {
            return 'selection_input';
        }

        // 创建遴选完成快照逻辑
        // ... existing snapshot creation code ...
        
        return 'usage';
    }

    // 处理准入分管领导审核状态
    private function handleAccessLeaderReviewState(Consumable $consumable, array $attributes): string
    {
        if (!isset($attributes['is_approved'])) {
            return 'access_leader_review';
        }

        if (!$attributes['is_approved']) {
            return 'selection_input';
        }

        // 创建遴选完成快照逻辑
        // ... existing snapshot creation code ...
        
        return 'usage';
    }

    // 新增处理分管院长审核状态
    private function handleSunshinePresidentReviewState(Consumable $consumable, array $attributes): string
    {
        if (!isset($attributes['is_approved'])) {
            return 'sunshine_president_review';
        }

        if (!$attributes['is_approved']) {
            return 'selection_input';
        }

        // 创建遴选完成快照
        // ... existing snapshot creation code ...
        
        return 'usage';
    }

    // 检查申请是否完整
    private function isApplicationComplete(array $attributes): bool
    {
        $requiredFields = [
            'platform_id',
            'department',
            'consumable',
            'model',
            'price',
            'registration_num',
            'company',
            'manufacturer',
            'category_zj',
            'parent_directory',
            'child_directory',
            'apply_type',
            'in_drugstore',
            'need_selection'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($attributes[$field]) || empty($attributes[$field])) {
                return false;
            }
        }

        return true;
    }

    // 检查文件是否上传
    private function hasRequiredFile(array $attributes, string $type): bool
    {
        if ($type === '阳光采购') {
            return isset($attributes['sunshine_purchase_file']) && !empty($attributes['sunshine_purchase_file']);
        } elseif ($type === '中标采购') {
            return isset($attributes['bid_purchase_file']) && !empty($attributes['bid_purchase_file']);
        }
        return false;
    }
} 