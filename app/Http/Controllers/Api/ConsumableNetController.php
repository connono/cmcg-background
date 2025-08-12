<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsumableNetResource;
use App\Models\ConsumableNet;
use Illuminate\Http\Request;
use App\Imports\ConsumableNetImport;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\Exporter\Exporter;
use App\Exports\ConsumableNetsExport;
use Rap2hpoutre\FastExcel\FastExcel;

class ConsumableNetController extends Controller
{
    public function index(Request $request) {
        $query = ConsumableNet::query();
        if (!is_null($request->consumable)) {
            $query = $query->where('consumable', 'like', '%'.$request->consumable.'%');
        }
        if (!is_null($request->manufacturer)) {
            $query = $query->where('manufacturer', 'like', '%'.$request->manufacturer.'%');
        }
        if (!is_null($request->registration_num)) {
            $query = $query->where('registration_num', 'like', '%'.$request->registration_num.'%');
        }
        if (!is_null($request->product_id)) {
            $query = $query->where('product_id', $request->product_id);
        }
        if (!is_null($request->specification)) {
            $query = $query->where('specification', $request->specification);
        }
        if (!is_null($request->model)) {
            $query = $query->where('model', $request->model);
        }
        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }
        if (!is_null($request->parent_directory)) {
            $query = $query->where('parent_directory', $request->parent_directory);
        }
        if (!is_null($request->child_directory)) {
            $query = $query->where('child_directory', $request->child_directory);
        }
        if (!is_null($request->purchase_category)) {
            $query = $query->where('purchase_category', $request->purchase_category);
        }
        if (!is_null($request->net_status)) {
            $query = $query->where('net_status', $request->net_status);
        }
        if (!is_null($request->consumable_encoding)) {
            $query = $query->where('consumable_encoding', 'like', '%'.$request->consumable_encoding.'%');
        }
        $query = $query->orderBy('price', direction: 'asc');

        if (!is_null($request->is_download)) {
            if($request->is_download == 'true') {
                return (new FastExcel($query->get()))->download('file.xlsx');
            }
        }
        $records = $query->paginate($request->pageSize);
        return  ConsumableNetResource::collection($records);
    }

    public function select(Request $request) {
        $original_net = ConsumableNet::where('product_id', $request->product_id)->first();
        
        // 计算原始net的最低价格（确保不为0）
        $original_price = $original_net->price > 0 ? $original_net->price : PHP_FLOAT_MAX;
        $original_tempory_price = $original_net->tempory_price > 0 ? $original_net->tempory_price : PHP_FLOAT_MAX;
        $original_lowest_price = min($original_price, $original_tempory_price);
        
        // 如果两个价格都是0或不存在，设置一个默认值
        if ($original_lowest_price == PHP_FLOAT_MAX) {
            $original_lowest_price = 0;
        }
        
        // 获取同一child_directory下的所有净
        $nets = ConsumableNet::where('child_directory', $original_net->child_directory)->get();
        
        // 用于存储结果的数组，以公司名称+最低价为键避免重复
        $unique_nets = [];
        
        // 统计以限价和中选价作为最低价格的记录数
        $tempory_price_count = 0;
        $price_count = 0;
        
        foreach ($nets as $net) {
            // 检查价格是否有效（>0）
            $price_valid = $net->price > 0;
            $tempory_price_valid = $net->tempory_price > 0;
            
            // 跳过两个价格都无效的情况
            if (!$price_valid && !$tempory_price_valid) {
                continue;
            }
            
            // 计算当前net的最低价格
            $price = $price_valid ? $net->price : PHP_FLOAT_MAX;
            $tempory_price = $tempory_price_valid ? $net->tempory_price : PHP_FLOAT_MAX;
            $lowest_price = min($price, $tempory_price);
            
            // 跳过无效价格
            if ($lowest_price == PHP_FLOAT_MAX) {
                continue;
            }
            
            // 只选择价格低于原始net的项目
            if ($original_lowest_price > 0 && $lowest_price < $original_lowest_price) {
                // 使用公司+最低价格作为唯一标识
                $key = $net->company . '_' . $lowest_price;
                
                if (!isset($unique_nets[$key])) {
                    $net->real_price = $lowest_price; // 添加一个额外属性用于排序
                    
                    // 判断最低价格是限价还是中选价
                    if ($lowest_price == $tempory_price) {
                        $net->price_type = 'tempory';
                        $tempory_price_count++;
                    } else {
                        $net->price_type = 'normal';
                        $price_count++;
                    }
                    
                    $unique_nets[$key] = $net;
                }
            }
        }
        
        // 将结果转为集合并按最低价格排序
        $selected_nets = collect(array_values($unique_nets))->sortBy('real_price');
        
        // 返回结果和统计数据
        return response()->json([
            'data' => ConsumableNetResource::collection($selected_nets),
            'statistics' => [
                'tempory_price_count' => $tempory_price_count,
                'price_count' => $price_count,
                'total' => $tempory_price_count + $price_count
            ]
        ]);
    }
}
