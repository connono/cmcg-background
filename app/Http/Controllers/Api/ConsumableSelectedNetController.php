<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ConsumableSelectedNetResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConsumableSelectedNet;


class ConsumableSelectedNetController extends Controller
{
    public function index (Request $request) {
        $query = ConsumableSelectedNet::query();
        if (!is_null($request->consumable_apply_id)) {
            $query = $query->where('consumable_apply_id', $request->consumable_apply_id);
        }
        $records = $query->paginate($request->pageSize);
        return ConsumableSelectedNetResource::collection($records);
    }

    public function bulkInsert(Request $request){
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.consumable_apply_id' => 'required|string',
            'items.*.model' => 'required|string',
            'items.*.manufacturer' => 'required|string',
            'items.*.registration_num' => 'required|string',
            'items.*.company' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.product_id' => 'required|string',
            'items.*.consumable_net_id' => 'required|string',
            'items.*.category' => 'required|string',
            'items.*.parent_directory' => 'required|string',
            'items.*.child_directory' => 'required|string',
        ]);

        // 插入数据时需要处理批量插入的格式
        $items = array_map(function ($item) {
            return [
                'consumable_apply_id' => $item['consumable_apply_id'],
                'model' => $item['model'],
                'manufacturer' => $item['manufacturer'],
                'registration_num' => $item['registration_num'],
                'company' => $item['company'],
                'price' => $item['price'],
                'product_id' => $item['product_id'],
                'consumable_net_id' => $item['consumable_net_id'],
                'category' => $item['category'],
                'parent_directory' => $item['parent_directory'],
                'child_directory' => $item['child_directory'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $validatedData['items']);

        // 使用处理后的 $items 数组进行插入
        ConsumableSelectedNet::insert($items);

        return response()->json(['message' => 'success'], 201);

    } 
}
