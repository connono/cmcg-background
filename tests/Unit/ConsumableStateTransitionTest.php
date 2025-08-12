<?php

namespace Tests\Unit;

use App\Models\Consumable;
use App\Models\ConsumableStateEvent;
use App\Services\ConsumableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpParser\Node\Expr\Cast\String_;
use Tests\TestCase;

class ConsumableStateTransitionTest extends TestCase
{

    protected Consumable $consumable;
    protected ConsumableService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(ConsumableService::class);

        // 创建测试耗材（需要遴选）
        $this->consumable = Consumable::create([
            'department' => '检验科',
            'consumable' => '测试耗材',
            'model' => 'TEST-001',
            'price' => 100.00,
            'apply_type' => '阳光采购',
            'need_selection' => true,
            'registration_num' => 'REG-001',
        ]);

        // 初始状态为 applied
        $this->consumable->currentState()->create([
            'state' => 'applied',
            'attributes' => [],
            'metadata' => ['created_by' => 1],
        ]);

        // 创建初始 application 快照
        ConsumableStateEvent::create([
            'consumable_id' => $this->consumable->id,
            'event_type' => 'snapshot:application',
            'from_state' => 'initial',
            'to_state' => 'applied',
            'attributes' => $this->consumable->toArray()
        ]);
    }

    /**
     * 测试申请阶段审核通过 → applied_finish → selection_input
     */
    public function test_application_approval_creates_valid_snapshots(): void
    {
        // 1. 初始状态为 applied
        $this->assertEquals('applied', $this->getConsumableState($this->consumable, 'snapshot:application'));
        $this->assertStateEvent('initial', 'applied');

        // 2. 审核通过申请阶段 → applied_finish
        $this->service->updateConsumableState($this->consumable, 'applied_finish');
        $this->consumable = $this->consumable->fresh();
        $this->assertEquals('applied_finish', $this->getConsumableState($this->consumable, 'snapshot:application'));
        // $this->assertStateEvent('applied', 'applied_finish');

        // // 3. 遴选录入阶段 → selection_input
        // $this->service->updateConsumableState($this->consumable, 'selection_input');
        // $this->consumable = $this->consumable->fresh();
        $this->assertEquals('selection_input', $this->getConsumableState($this->consumable, 'snapshot:selection')); 
        // $this->assertStateEvent('applied_finish', 'selection_input');
        // $this->assertSnapshotCreated('snapshot:selection');

        // 4. 验证 application 快照已更新 to_state → applied_finish
        $applicationSnapshot = ConsumableStateEvent::where([
            'consumable_id' => $this->consumable->id,
            'event_type' => 'snapshot:application',
        ])->first();
        $this->assertEquals('applied_finish', $this->getConsumableState($this->consumable, 'snapshot:application'));

        // 5. 验证 selection 快照 to_state → selection_input
        $selectionSnapshot = ConsumableStateEvent::where([
            'consumable_id' => $this->consumable->id,
            'event_type' => 'snapshot:selection',
        ])->first();
        $this->assertEquals('selection_input', $this->getConsumableState($this->consumable, 'snapshot:selection'));
        
        $attributes = [
            'department' => '检验科',
            'consumable' => '测试耗材11',
            'model' => 'TEST-002',
            'price' => 80.00,
            'apply_type' => '阳光采购',
            'registration_num' => 'REG-002',
            'sunshine_purchase_file' => 'selection_file.pdf',
        ];

        $this->service->updateConsumableState($this->consumable, 'medical_engineering_review', $attributes);
        $this->consumable = $this->consumable->fresh();
        $this->assertEquals('测试耗材11', $this->consumable->consumable);
        $this->assertEquals('TEST-002', $this->consumable->model);
        $this->assertEquals(80.00, $this->consumable->price);
        $this->assertEquals('阳光采购', $this->consumable->apply_type);
        $this->assertEquals('REG-002', $this->consumable->registration_num);
        $this->assertEquals('selection_file.pdf', $this->consumable->sunshine_purchase_file);
          
        $this->assertEquals('medical_engineering_review', $this->getConsumableState($this->consumable, 'snapshot:selection'));
        $this->assertStateEvent('selection_input', 'medical_engineering_review');
        
        $this->service->updateConsumableState($this->consumable, 'sunshine_president_review');
        $this->consumable = $this->consumable->fresh();
        $this->assertEquals('sunshine_president_review', $this->getConsumableState($this->consumable, 'snapshot:selection'));
        $this->assertStateEvent('medical_engineering_review', 'sunshine_president_review');
        
        $this->service->updateConsumableState($this->consumable, 'selection_finish');
        $this->consumable = $this->consumable->fresh();
        $this->assertEquals('selection_finish', $this->getConsumableState($this->consumable, 'snapshot:selection'));
        $this->assertStateEvent('sunshine_president_review', 'selection_finish');

        $this->service->updateConsumableState($this->consumable, 'in_use');
        $this->consumable = $this->consumable->fresh();
        $this->assertEquals('in_use', $this->getConsumableState($this->consumable, 'snapshot:usage'));
        $this->assertStateEvent('selection_finish', 'in_use');
    }

    protected function getConsumableState(Consumable $consumable, string $type): String
    {
        $snapshot = ConsumableStateEvent::where([
            'consumable_id' => $consumable->id,
            'event_type' => $type
        ])->first();

        return $snapshot->to_state;
    }

    /**
     * 断言状态事件存在
     */
    protected function assertStateEvent(string $fromState, string $toState): void
    {
        $event = ConsumableStateEvent::where([
            'consumable_id' => $this->consumable->id,
            'from_state' => $fromState,
            'to_state' => $toState
        ])->first();

        $this->assertNotNull($event, "未找到状态事件：{$fromState} → {$toState}");
    }

    /**
     * 断言快照创建
     */
    protected function assertSnapshotCreated(string $eventType): void
    {
        $snapshot = ConsumableStateEvent::where([
            'consumable_id' => $this->consumable->id,
            'event_type' => $eventType
        ])->first();

        $this->assertNotNull($snapshot, "未找到快照：{$eventType}");
    }
}