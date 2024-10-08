<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ConsumableApplyResource;
use App\Models\Department;
use App\Models\ConsumableApplyTable;
use App\Models\Notification;


class ConsumableApplyController extends Controller
{
    public function getSerialNumber(Request $request, ConsumableApplyTable $record){
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
        ])->setStatusCode(200);
    }


    public function store(Request $request){
        if(!\Cache::has('consumable_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $record = ConsumableApplyTable::create([
            "platform_id" => $request->platform_id,
            "department" => $request->department,
            "consumable" => $request->consumable,
            "model" => $request->model,
            "price" => $request->price,
            "apply_date" => $request->apply_date,
            "count_year" => $request->count_year,
            "registration_num" => $request->registration_num,
            "company"=> $request->company,
            "manufacturer" => $request->manufacturer,
            "category_zj" => $request->category_zj,
            "parent_directory" => $request->parent_directory,
            "child_directory" => $request->child_directory,
            "apply_type" => $request->apply_type,
            "pre_assessment" => $request->pre_assessment,
            "final" => $request->final,
            "apply_file" => $request->apply_file,
            "in_drugstore" => $request->in_drugstore,
        ]);
        $record->serial_number = $request->serial_number;
        if($request->final == '1'){
            $record->status = '3';
            $record->save();
        }else{
            if($request->in_drugstore == '0'){
                $record->status = '2';
                $record->save();
                $notification = Notification::create([
                    'permission' => 'can_purchase_consumable_record',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_apply',
                    'type' => 'engineer_approve', 
                    'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }else{
                if($request->apply_type == '0' || $request->apply_type == '4'){
                    $record->status = '2';
                    $record->save();
                    $notification = Notification::create([
                        'permission' => 'can_purchase_consumable_record',
                        'title' => $record->consumable,
                        'body' => json_encode($record),
                        'category' => 'consumable',
                        'n_category' => 'consumable_apply',
                        'type' => 'engineer_approve', 
                        'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification);
                }else{
                    $record->status = '0';
                    $record->save();
                    $notification = Notification::create([
                        'permission' => 'can_purchase_consumable_record',
                        'title' => $record->consumable,
                        'body' => json_encode($record),
                        'category' => 'consumable',
                        'n_category' => 'consumable_apply',
                        'type' => 'buy', 
                        'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification);
                }
            } 
        }

        \Cache::forget('consumable_serial_number_'.$request->serial_number);

        return new ConsumableApplyResource($record);
    }

    public function index(Request $request, ConsumableApplyTable $record){
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
        
        return  ConsumableApplyResource::collection($records);
    }

    public function update(Request $request, ConsumableApplyTable $record){
        if($request->method === 'approve') {
            if($request->approve == '0'){  //审批通不过
                $attributes['status'] = '0';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_purchase_consumable_record',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_apply',
                    'type' => 'buy', 
                    'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }elseif($request->approve == '1'){ //审核通过
                $attributes['status'] = '2';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_engineer_approve_consumable_record',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_apply',
                    'type' => 'engineer_approve', 
                    'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }    
        } else if ($request->method === 'engineer_approve') {
            if($request->approve == '0'){  //审批通不过
                $attributes['status'] = '0';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_purchase_consumable_record',
                    'title' => $record->consumable,
                    'body' => json_encode($record),
                    'category' => 'consumable',
                    'n_category' => 'consumable_apply',
                    'type' => 'buy', 
                    'link' => '/consumable/list/apply/detail#update&' . $record->serial_number,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
            }elseif($request->approve == '1'){ //审核通过
                $attributes['status'] = '3';
                $record->update($attributes);
                $record->notification()->delete();
            }    
        }
        return new ConsumableApplyResource($record);
    }
    public function getItem(Request $request, ConsumableApplyTable $record) {
        $record = ConsumableApplyTable::where('serial_number', $request->serial_number)->orderBy('id', 'DESC')->first();
        return new ConsumableApplyResource($record);
    }

    // public function stop(Request $request, ConsumableTemporaryApply $record){
        
    //         $attributes = $request->only(['stop_reason']);
    //             $attributes['status'] = '4';
    //             $record->update($attributes);
    

    // }
}
