function getSpecificSnapshot(Consumable $consumable, $snapshotId): JsonResponse
{
    try {
        // 增加日志记录和更明确的错误提示
        \Log::info("Attempting to fetch snapshot", [
            'consumable_id' => $consumable->id,
            'snapshot_id' => $snapshotId
        ]);

        $snapshot = ConsumableStateEvent::where('id', $snapshotId)
            ->where('consumable_id', $consumable->id)
            ->first();

        // 显式检查是否存在
        if (!$snapshot) {
            return response()->json([
                'message' => '未找到指定快照，请确认ID和耗材ID是否正确',
                'data' => [
                    'consumable_id' => $consumable->id,
                    'snapshot_id' => $snapshotId
                ]
            ], 404);
        }

        return response()->json([
            'message' => '操作成功',
            'data' => new ConsumableStateResource($snapshot),
        ]);
    } catch (\Exception $e) {
        \Log::error("Snapshot fetch error: {$e->getMessage()}", [
            'exception' => $e
        ]);
        
        return response()->json([
            'message' => '服务器内部错误，请稍后再试',
            'error' => $e->getMessage()
        ], 500);
    }
}