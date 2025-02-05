<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ConsumableTrendsResource;
use App\Models\Department;
use App\Models\ConsumableTrendsTable;
use App\Models\ConsumableApplyTable;
use App\Models\ConsumableDirectoryTable;
use App\Models\Notification;
use App\Models\Leader;
use App\Models\User;

class ConsumableTrendsController extends Controller
{
    public function index(Request $request) {
        $query = ConsumableTrendsTable::query();
        $records = $query->paginate($request->pageSize);
        return  ConsumableTrendsResource::collection($records);
    }

    public function store(Request $request){        

        $record = ConsumableTrendsTable::create([
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
            "contract_file" => $request->contract_file,
            "is_need" => $request->is_need,
            "reason" => $request->reason,
        ]);
        if($request->method == 'apply'){
            $consumable_apply_table = ConsumableApplyTable::query()->where('serial_number', $request->consumable_apply_id)->first();
            $consumable_apply_table->update(['status' => '1']);
            $department = Department::where('label', '医学工程科')->first();
            $leader = Leader::find($department->leader_id);
            $user = User::where('name', $leader->name)->first();
            $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $record->consumable,
            'body' => json_encode($consumable_apply_table),
            'category' => 'consumable',
            'n_category' => 'consumable_apply',
            'type' => 'vertify', 
            'link' => '/consumable/list/apply/detail#update&' . $consumable_apply_table->serial_number,
            ]);
            $consumable_apply_table->notification()->delete();
            $consumable_apply_table->notification()->save($notification);
        }elseif ($request->method == 'directory'){
            $consumable_directory_table = ConsumableDirectoryTable::query()->where('consumable_apply_id', $request->consumable_apply_id)->first();
            $consumable_directory_table->update(['status' => '2']);
            $department = Department::where('label', '医学工程科')->first();
            $leader = Leader::find($department->leader_id);
            $user = User::where('name', $leader->name)->first();
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $record->consumable,
                'body' => json_encode($consumable_directory_table),
                'category' => 'consumable',
                'n_category' => 'consumable_list',
                'type' => 'vertify', 
                'link' => '/consumable/list/index/detail#update&' . $consumable_directory_table->consumable_apply_id,
                ]);
        }
    }

    public function update(Request $request, ConsumableApplyTable $record){
        
            $attributes = $request->only(['platform', 'price','company']);
                $attributes['status'] = '2';
                $record->update($attributes);
          
    }
    public function getLastItem(Request $request, ConsumableTrendsTable $record) {
        $record = ConsumableTrendsTable::where('consumable_apply_id', $request->serial_number)->orderBy('id', 'DESC')->first();
        return new ConsumableTrendsResource($record);
    }
    public function getFirstItem(Request $request, ConsumableTrendsTable $record) {
        $record = ConsumableTrendsTable::where('consumable_apply_id', $request->serial_number)->orderBy('id', 'ASC')->first();
        return new ConsumableTrendsResource($record);
    }
    public function index(Request $request, ConsumableTrendsTable $record){
        $records = ConsumableTrendsTable::where('consumable_apply_id', $request->serial_number)->orderBy('id', 'DESC')->get(); 
        return  $records;
    }
}
