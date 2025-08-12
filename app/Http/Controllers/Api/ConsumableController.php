<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsumableStoreRequest;
use App\Http\Resources\ConsumableResource;
use App\Http\Resources\ConsumableStateResource;
use App\Models\Consumable;
use App\Models\ConsumableStateEvent;
use App\Services\ConsumableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConsumableController extends Controller
{
    /**
     * @var ConsumableService
     */
    protected ConsumableService $consumableService;

    /**
     * 构造函数
     *
     * @param ConsumableService $consumableService
     */
    public function __construct(ConsumableService $consumableService)
    {
        $this->consumableService = $consumableService;
    }

    /**
     * 创建新的耗材申请记录
     */
    public function store(ConsumableStoreRequest $request): JsonResponse
    {
        try {
            $result = $this->consumableService->createConsumable($request->validated());

            return response()->json([
                'message' => '耗材申请创建成功',
                'data' => [
                    'consumable' => new ConsumableResource($result['consumable']),
                    'state' => new ConsumableStateResource($result['state']),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '创建失败',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取指定 ID 的耗材详情（包含最新属性和所有历史快照）
     */
    public function show(Consumable $consumable): JsonResponse
    {
        try {
            $consumable->load([
                'currentState', 
                'stateEvents' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ]);

            return response()->json([
                'message' => '操作成功',
                'data' => new ConsumableResource($consumable)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '获取耗材详情失败',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取指定耗材的某条历史快照数据
     */
    public function getSpecificSnapshot(Consumable $consumable, $snapshotId): JsonResponse
    {
        $snapshot = ConsumableStateEvent::where('id', $snapshotId)
            ->where('consumable_id', $consumable->id)
            ->firstOrFail();

        return response()->json([
            'snapshot' => $snapshot->attributes,
            'status' => $snapshot->to_state,
            'metadata' => $snapshot->metadata,
            'snapshot_type' => $this->extractSnapshotType($snapshot->event_type)
        ]);
    }

    private function extractSnapshotType(string $eventType): ?string
    {
        if (str_starts_with($eventType, 'snapshot:')) {
            return substr($eventType, 9);
        }
        return null;
    }

    /**
     * 根据状态筛选耗材申请列表
     */
    public function getCurrentConsumables(Request $request): AnonymousResourceCollection
    {
        $query = Consumable::query();

        if ($request->has('states')) {
            $states = explode(',', $request->states);
            $query->whereHas('currentState', function ($q) use ($states) {
                $q->whereIn('state', $states);
            });
        }


        // 科室筛选
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        // 耗材名称筛选
        if ($request->has('consumable')) {
            $query->where('consumable', 'like', '%' . $request->consumable . '%');
        }

        // 申请类型筛选
        if ($request->has('apply_type')) {
            $query->where('apply_type', $request->apply_type);
        }

        // 时间范围筛选（耗材创建时间）
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // 排序
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // 分页
        $perPage = $request->get('per_page', 15);
        $records = $query->paginate($perPage);

        // 加载历史记录（可选）
        $records->getCollection()->transform(function ($consumable) {
            $consumable->load(['stateEvents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }]);
            return $consumable;
        });

        return ConsumableResource::collection($records);
    }

/**
     * 查询历史快照（支持按快照状态筛选）
     */
    public function getHistoricalSnapshots(Request $request): JsonResponse
    {
        try {
            $states = explode(',', $request->input('states'));
            // return response()->json($states);
            $snapshots = ConsumableStateEvent::whereIn('to_state', $states)->where('event_type', $request->input('event_type'))->paginate(15);
            return response()->json($snapshots);
        } catch (\Exception $e) {
            return response()->json([
                'error' => '获取历史快照失败',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新耗材状态
     */
    public function update(Request $request, Consumable $consumable): JsonResponse
    {
        try {
            $currentState = $consumable->currentState->state;
            $request->validate(['approved' => 'required|boolean']);
            $approved = $request->boolean('approved');
            $nextState = null;
            $attributes = [];

            switch ($currentState) {
                case 'applied':
                    // 初始申请审核
                    if ($approved) {
                        $nextState = 'applied_finish';
                        $this->consumableService->updateConsumableState(
                            $consumable,
                            $nextState,
                            $attributes
                        );
                        $nextState = $consumable->need_selection ? 'selection_input' : 'in_use';
                    } else {
                        $nextState = 'disabled';
                    }
                    break;
                case 'selection_input':
                    // 遴选录入阶段审核
                    if ($approved) {
                        // 通用属性更新
                        $request->validate([
                            'selection_reason' => 'nullable|string|max:500',
                            'estimated_cost' => 'nullable|numeric|min:0',
                            'supplier_info' => 'nullable|string|max:255'
                        ]);
                        
                        $attributes = array_filter([
                            'selection_reason' => $request->selection_reason,
                            'estimated_cost' => $request->estimated_cost,
                            'supplier_info' => $request->supplier_info
                        ]);

                        // 根据采购类型进入下一步审核
                        if ($consumable->apply_type === '阳光采购') {
                            $request->validate(['sunshine_purchase_file' => 'required|string|max:255']);
                            $attributes['sunshine_purchase_file'] = $request->sunshine_purchase_file;
                            $nextState = 'medical_engineering_review';  // 阳光采购：医工科审核
                        } else {
                            $request->validate(['bid_purchase_file' => 'required|string|max:255']);
                            $attributes['bid_purchase_file'] = $request->bid_purchase_file;
                            $nextState = 'access_leader_review';  // 中标采购：直接准入审核
                        }
                    } else {
                        return response()->json([
                            'message' => '遴选录入状态不允许拒绝操作',
                            'error' => 'Invalid operation'
                        ], 400);
                    }
                    break;

                case 'medical_engineering_review':
                    // 阳光采购 - 医工科审核
                    if ($approved) {
                        // ✅ 审核通过后进入分管院长审核
                        $nextState = 'sunshine_president_review';
                    } else {
                        $nextState = 'selection_input';  // 驳回后回到遴选录入
                    }
                    break;

                case 'access_leader_review':
                    // 中标采购 - 准入领导审核
                    if ($approved) {
                        $nextState = 'usage';  // 直接进入使用阶段
                    } else {
                        $nextState = 'selection_input';
                    }
                    break;

                case 'sunshine_president_review':
                    // 阳光采购 - 分管院长最终审核
                    if ($approved) {
                        $nextState = 'usage';  // 最终审核通过
                    } else {
                        $nextState = 'selection_input';
                    }
                    break;

                default:
                    return response()->json([
                        'message' => '当前状态不允许进行审核操作',
                        'error' => 'Invalid state'
                    ], 400);
            }

            $result = $this->consumableService->updateConsumableState(
                $consumable,
                $nextState,
                $attributes
            );

            return response()->json([
                'message' => '审核操作成功',
                'data' => [
                    'consumable' => new ConsumableResource($result['consumable']),
                    'state' => new ConsumableStateResource($result['state'])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '审核操作失败',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}