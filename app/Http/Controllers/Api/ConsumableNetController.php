<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsumableNetResource;
use App\Models\ConsumableNet;
use Illuminate\Http\Request;
use App\Imports\ConsumableNetImport;
use Maatwebsite\Excel\Facades\Excel;

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
        $records = $query->paginate($request->pageSize);
        return  ConsumableNetResource::collection($records);
    }
}
