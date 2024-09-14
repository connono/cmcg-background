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
    /*public function getSerialNumber(Request $request, ConsumableApplyTable $record){
        $query = $record->query();
        $count = $query->count();
        if($count!==0){
            $record = $query->orderBy('id', 'DESC')->get()->first();
            $serial_number = intval($record->serial_number);
            if(date("Y")==floor($serial_number/10000)) {
                $serial_number = $serial_number+1;
            } else {
                $serial_number = date("Y")*10000+1;
            }
        } else {
            $serial_number = date("Y")*10000+1;
        }
        while(\Cache::has('consumable_serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'consumable_serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['consumable_serial_number_'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
    }

*/
    public function store(Request $request){
       /* if(!\Cache::has('consumable_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }
*/          

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
/*
        $notification = Notification::create([
            'permission' => 'can_survey_equipment',
            'title' => $record->equipment,
            'body' => json_encode($record),
            'category' => 'apply',
            'n_category' => 'equipmentApplyRecord',
            'type' => 'survey', 
            'link' => '/apply/equipment/detail#update&' . $record->id,
        ]);
        $record->notification()->delete();
        $record->notification()->save($notification);

        \Cache::forget('consumable_serial_number_'.$request->serial_number);

        return new ConsumableApplyResource($record);*/
    }

  /*  public function index(Request $request, ConsumableApplyTable $record){
        $query = $record->query();
       
        if (!is_null($request->department)) {
            $department = Department::where('name', $request->department)->first();
            $query = $query->where('department', $department->label);
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
            $query = $query->where('platform_id', $request->platform_id);
        }
        if (!is_null($request->company)) {
            $query = $query->where('company',  'like', '%'.$request->company.'%');
        }
        if (!is_null($request->isPaginate)) {
            $records = $query->paginate();
        } else {
            $records = $query->get();
        }
        
        return  ConsumableApplyResource::collection($records);
    }
*/
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

    /*public function stop(Request $request, ConsumableTemporaryApply $record){
        
            $attributes = $request->only(['stop_reason']);
                $attributes['status'] = '4';
                $record->update($attributes);
    

    }

    public function layout(Request $request, ConsumableTemporaryApply $record){
        $query = $record->query();
        return new ConsumableTemporaryApplyRecordResource($record); 
    }*/
}
