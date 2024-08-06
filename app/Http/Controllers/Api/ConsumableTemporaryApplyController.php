<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\ConsumableTemporaryApplyRecordResource;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsunmableTemporaryApply;
class ConsumableTemporaryApplyController extends Controller
{
    //
    public function getSerialNumber(Request $request, ConsunmableTemporaryApply $record){
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
        while(\Cache::has('consunmabletemporary_serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'consunmabletemporary_serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['consunmabletemporary_serial_number_'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
    }


    public function store(Request $request){
        if(!\Cache::has('consunmabletemporary_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $record = ConsunmableTemporaryApply::create([
            'department' => $request->department,
            'consumable' => $request->consumable,
            'count' => $request->count,
            'budget' => $request->budget,
            'model' => $request->model,
            'manufacturer' => $request->manufacturer,
            'telephone' => $request->telephone,
            'registration_num' => $request->registration_num,
            'reason' => $request->reason,
            'apply_date' => $request->apply_date,
            'apply_type' => $request->apply_type,
            'apply_file' => $request->apply_file,
        ]);
        $record->serial_number = $request->serial_number;
        $record->status = '1';

        $record->save();
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
*/
        \Cache::forget('consunmabletemporary_serial_number_'.$request->serial_number);

        return new ConsumableTemporaryApplyRecordResource($record);
    }

    public function index(Request $request, ConsunmableTemporaryApply $record){
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
        if (!is_null($request->product_id)) {
            $query = $query->where('product_id', $request->product_id);
        }
        if (!is_null($request->company)) {
            $query = $query->where('company',  'like', '%'.$request->company.'%');
        }
        if (!is_null($request->isPaginate)) {
            $records = $query->paginate();
        } else {
            $records = $query->get();
        }
        
        return  ConsumableTemporaryApplyRecordResource::collection($records);
    }

    public function update(Request $request, ConsunmableTemporaryApply $record){
        $method = $request->method;
        if($method === 'buy'){
            $attributes = $request->only(['product_id','arrive_date', 'arrive_price','company','telephone2','accept_file']);
                $attributes['status'] = '2';
                $record->update($attributes);
        }elseif($method === 'vertify'){
            $attributes['status'] = '3';
            $record->update($attributes);
        }


    }

    public function stop(Request $request, ConsunmableTemporaryApply $record){
        
            $attributes = $request->only(['stop_reason']);
                $attributes['status'] = '4';
                $record->update($attributes);
    

    }

    public function layout(Request $request, ConsunmableTemporaryApply $record){
        $query = $record->query();
        return new ConsumableTemporaryApplyRecordResource($record); 
    }
}


