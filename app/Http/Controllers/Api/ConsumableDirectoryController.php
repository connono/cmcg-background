<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ConsumableDirectoryResource;
use App\Models\Department;
use App\Models\ConsumableDirectoryTable;
use App\Models\ConsumableApplyTable;
use App\Models\ConsumableTrendsTable;
use App\Models\Notification;

class ConsumableDirectoryController extends Controller
{
    public function store(Request $request){

         if($request->vertify == '0'){  //审核通不过
            $consumable_apply_table = ConsumableApplyTable::query()->where('serial_number', $request->consumable_apply_id)->first();
            $consumable_apply_table->update(['status' => '0']);
            $notification = Notification::create([
                'permission' => 'can_purchase_consumable_record',
                'title' => $consumable_apply_table->consumable,
                'body' => json_encode($consumable_apply_table),
                'category' => 'consumable',
                'n_category' => 'consumable_list',
                'type' => 'buy', 
                'link' => '/consumable/list/index/detail#update&' . $consumable_apply_table->serial_number,
            ]);
            $consumable_apply_table->notification()->delete();
            $consumable_apply_table->notification()->save($notification);
           return response()->json(['data' => ''])->setStatusCode(200);
        }elseif($request->vertify == '1'){ //审核通过
            $consumable_apply_table = ConsumableApplyTable::query()->where('serial_number', $request->consumable_apply_id)->first();
            $consumable_apply_table->update(['status' => '3']);
            $consumable_apply_table->notification()->delete();
            
            $record = ConsumableDirectoryTable::create([
                "consumable_apply_id" => $request->consumable_apply_id,
                "platform_id" => $request->platform_id,
                "department" => $request->department,
                "consumable" => $request->consumable,
                "model" => $request->model,
                "price" => $request->price,
                "start_date" => $request->start_date,
                "exp_date" => $request->exp_date,
                "registration_num" => $request->registration_num,
                "company"=> $request->company,
                "manufacturer" => $request->manufacturer,
                "category_zj" => $request->category_zj,
                "parent_directory" => $request->parent_directory,
                "child_directory" => $request->child_directory,
                "apply_type" => $request->apply_type,
                "in_drugstore" => $request->in_drugstore,
                "status" => '0',
            ]);
            return new ConsumableDirectoryResource($record);
        }
     }


     public function update(Request $request, ConsumableDirectoryTable $record){
        if($request->method == 'approve'){
            if($request->approve == '0'){  //审批通不过
                $attributes['status'] = '1';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_purchase_consumable_list',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_list',
                    'type' => 'buy', 
                    'link' => '/consumable/list/index/detail#update&' . $record->consumable_apply_id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }elseif($request->approve == '1'){//审批通过
                $attributes['status'] = '3';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_engineer_approve_consumable_list',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_list',
                    'type' => 'engineer_approve', 
                    'link' => '/consumable/list/index/detail#update&' . $record->consumable_apply_id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);

            }
        } elseif($request->method == 'vertify'){
            if($request->vertify == '0'){  //审核通不过
                $attributes['status'] = '1';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_purchase_consumable_list',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_list',
                    'type' => 'buy', 
                    'link' => '/consumable/list/index/detail#update&' . $record->consumable_apply_id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }elseif($request->vertify == '1'){ //审核通过
                $record2 =ConsumableTrendsTable::where('consumable_apply_id', $request->consumable_apply_id)
                ->orderBy('id', 'DESC')->first();
                $attributes['platform_id'] = $record2->platform_id;
                $attributes['consumable'] = $record2->consumable;
                $attributes['model'] = $record2->model;
                $attributes['price'] = $record2->price;
                $attributes['start_date'] = $record2->start_date;
                $attributes['registration_num'] = $record2->registration_num;
                $attributes['company'] = $record2->company;
                $attributes['manufacturer'] = $record2->manufacturer;
                $attributes['category_zj'] = $record2->category_zj;
                $attributes['parent_directory'] = $record2->parent_directory;
                $attributes['child_directory'] = $record2->child_directory;
                $attributes['apply_type'] = $record2->apply_type;
                $attributes['exp_date'] = $request->exp_date;
                $attributes['status'] = '0';
                $record->update($attributes);
                $record->notification()->delete();
            }

           
        }elseif($request->method == 'stop'){
            $attributes['stop_reason'] =$request->stop_reason;
            $attributes['stop_date'] = $request->stop_date;
            $attributes['status'] = '4';
            $record->update($attributes);
        }
       /* $attributes = $request->only(['platform', 'price','company']);
            $attributes['status'] = '2';
            $record->update($attributes);*/
      
}

public function index(Request $request, ConsumableDirectoryTable $record){
    $query = $record->query();
   
    if (!is_null($request->department)) {
        $query = $query->where('department', $request->department);
    }
    if (!is_null($request->status)) {
        $query = $query->where('status', $request->status);
    }
    if (!is_null($request->consumable)) {
        $query = $query->where('consumable', 'like', '%'.$request->consumable.'%');
    }
    if (!is_null($request->apply_type)) {
        $query = $query->where('apply_type', $request->apply_type);
    }
    if (!is_null($request->platform_id)) {
        $query = $query->where('platform_id', 'like', '%'.$request->platform_id.'%');
    }
    if (!is_null($request->company)) {
        $query = $query->where('company',  'like', '%'.$request->company.'%');
    }
    if (!is_null($request->isPaginate)) {
        $records = $query->paginate();
    } else {
        $records = $query->get();
    }
    
    return  ConsumableDirectoryResource::collection($records);
}
public function getItem(Request $request, ConsumableDirectoryTable $record) {
    $record = ConsumableDirectoryTable::where('consumable_apply_id', $request->serial_number)->first();
    return new ConsumableDirectoryResource($record);
}
public function stop(Request $request, ConsumableDirectoryTable $record){
    $attributes = $request->only(['stop_reason']);
    $attributes['status'] = '4';
    $record->update($attributes);
}
 
}
