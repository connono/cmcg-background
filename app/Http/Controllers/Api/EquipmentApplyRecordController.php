<?php

namespace App\Http\Controllers\Api;

use App\Models\EquipmentApplyRecord;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Http\Resources\EquipmentApplyRecordResource;
use App\Http\Requests\Api\EquipmentApplyRecordRequest;
use App\Models\Notification;

class EquipmentApplyRecordController extends Controller
{

    public function index(Request $request, EquipmentApplyRecord $record){
        $query = $record->query();
        if (!is_null($request->department)) {
            $department = Department::where('name', $request->department)->first();
            $query = $query->where('department', $department->label);
        }
        if (!is_null($request->status)) {
            if ($request->status == 'stop') {
                $query = $query->where('is_stop', 'true');
            } else {
                $query1 = EquipmentApplyRecord::where('is_stop', null)->where('status', '' . $request->status);
                $query2 = EquipmentApplyRecord::where('is_stop', 'false')->where('status', '' . $request->status);
                $query = $query1->union($query2);
            }
        }
        if (!is_null($request->equipment)) {
            $query = $query->where('equipment', 'like', '%'.$request->equipment.'%');
        }
        if (!is_null($request->apply_type)) {
            $query = $query->where('apply_type', $request->apply_type);
        }
        if (!is_null($request->purchase_type)) {
            $query = $query->where('purchase_type', $request->purchase_type);
        }
        if (!is_null($request->isAdvance)) {
            $query = $query->where('isAdvance', $request->isAdvance);
        }
        if (!is_null($request->isPaginate)) {
            $records = $query->paginate();
        } else {
            $records = $query->get();
        }
        
        return  EquipmentApplyRecordResource::collection($records);
    }

    public function getItem(Request $request, EquipmentApplyRecord $record){
        $record = EquipmentApplyRecord::find($request->id);
        return new EquipmentApplyRecordResource($record);
    }

    public function getSerialNumber(Request $request, EquipmentApplyRecord $record){
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
        while(\Cache::has('equipment_serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'equipment_serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['equipment_serial_number_'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
    }

    public function store(EquipmentApplyRecordRequest $request){
        if(!\Cache::has('equipment_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $record = EquipmentApplyRecord::create([
            'equipment' => $request->equipment,
            'count' => $request->count,
            'department' => $request->department,
            'budget' => $request->budget,
            'apply_type' => $request->apply_type,
            'apply_picture' => $request->apply_picture,
        ]);
        $record->serial_number = $request->serial_number;
        $record->status = '1';

        $record->save();

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

        \Cache::forget('equipment_serial_number_'.$request->serial_number);

        return new EquipmentApplyRecordResource($record);
    }

    public function update(EquipmentApplyRecordRequest $request, $method, EquipmentApplyRecord $record){
        switch($request->method){
            case 'survey':
                $attributes = $request->only(['survey_date','purchase_type','survey_record','meeting_record', 'survey_picture', 'is_stop', 'stop_reason']);
                $attributes['status'] = '2';
                $record->update($attributes);
                if ($request->is_stop == 'true') {
                    $record->notification()->delete();
                } else {
                    $notification = Notification::create([
                        'permission' => 'can_approve_equipment',
                        'title' => $record->equipment,
                        'body' => json_encode($record),
                        'category' => 'apply',
                        'n_category' => 'equipmentApplyRecord',
                        'type' => 'approve',
                        'link' => '/apply/equipment/detail#update&' . $record->id,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification);
                }
                break;
            case 'approve':
                $attributes = $request->only(['approve_date','execute_date', 'approve_picture']);
                if ($record->purchase_type == 1) {
                    $attributes['status'] = '3';
                    $record->update($attributes);
                    $notification = Notification::create([
                        'permission' => 'can_tender_equipment',
                        'title' => $record->equipment,
                        'body' => json_encode($record),
                        'category' => 'apply',
                        'n_category' => 'equipmentApplyRecord',
                        'type' => 'tender',
                        'link' => '/apply/equipment/detail#update&' . $record->id,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification);    
                } else {
                    $attributes['status'] = '4';
                    $record->update($attributes);
                    $notification = Notification::create([
                        'permission' => 'can_contract_equipment',
                        'title' => $record->equipment,
                        'body' => json_encode($record),
                        'category' => 'apply',
                        'n_category' => 'equipmentApplyRecord',
                        'type' => 'contract',
                        'link' => '/apply/equipment/detail#update&' . $record->id,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification); 
                }
                break;
            case 'tender':
                $attributes = $request->only(['tender_date','tender_out_date', 'tender_file', 'tender_boardcast_file', 'bid_winning_file', 'send_tender_file']);
                $attributes['status'] = '4';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_contract_equipment',
                    'title' => $record->equipment,
                    'body' => json_encode($record),
                    'category' => 'apply',
                    'n_category' => 'equipmentApplyRecord',
                    'type' => 'contract',
                    'link' => '/apply/equipment/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'install':
                $attributes = $request->only(['install_date', 'install_picture']);
                $attributes['status'] = '6';
                $attributes['advance_status'] = '0';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_engineer_approve_equipment',
                    'title' => $record->equipment,
                    'body' => json_encode($record),
                    'category' => 'apply',
                    'n_category' => 'equipmentApplyRecord',
                    'type' => 'engineer_approve',
                    'link' => '/apply/equipment/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'engineer_approve':
                $attributes = $request->only(['isAdvance']);
                $attributes['status'] = '7';
                $attributes['advance_status'] = '0';
                $record->update($attributes);
                $notification = Notification::create([
                    'permission' => 'can_warehouse_equipment',
                    'title' => $record->equipment,
                    'body' => json_encode($record),
                    'category' => 'apply',
                    'n_category' => 'equipmentApplyRecord',
                    'type' => 'warehouse',
                    'link' => '/apply/equipment/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'warehouse':
                $attributes = $request->only(['warehousing_date']);
                $attributes['status'] = '8';
                $record->update($attributes);
                $contract =  $record->contract()->first();
                $json = json_encode($contract, true);
                $contract_array = json_decode($json, true);
                $information = (object) array_merge([
                    'equipment' => $record->equipment,
                    'count' => $record->count,
                    'budget' => $record->budget,
                ], $contract_array);
                $notification = Notification::create([
                    'permission' => 'can_engineer_approve_equipment',
                    'title' => $record->equipment,
                    'body' => json_encode($information),
                    'category' => 'apply',
                    'n_category' => 'equipmentApplyRecord',
                    'type' => 'finish',
                    'link' => '/purchase/contract/detail#' . $contract->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
        }
        return new EquipmentApplyRecordResource($record);
    }

    public function delete(Request $request, EquipmentApplyRecord $record){
        $record->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function back(Request $request, EquipmentApplyRecord $record){
        if (!is_null($record->advance_status) && $record->advance_status != '0') {
            return response()->json(['data' => '无法回退'])->setStatusCode(200);
        }
        switch ($record->status) {
            case '0':
                break;
            case '1':
                break;
            case '2':
                $record->update([
                    'status' => '1',
                    'survey_date' => null,
                    'purchase_type' => null,
                    'survey_record' => null,
                    'meeting_record' => null,
                    'survey_picture' => null,
                ]);
                $record->notification()->delete();
                break;
            case '3':
                $record->update([
                    'status' => '2',
                    'approve_date' => null,
                    'execute_date' => null,
                    'approve_picture' => null,
                ]);
                $record->notification()->delete();
                break;
            case '4':
                $record->update([
                    'status' => $record->purchase_type == '1' ? '3' : '2', 
                    'tender_date' => null,
                    'tender_file' => null,
                    'tender_boardcast_file' => null,
                    'tender_out_date' => null,
                    'bid_winning_file' => null,
                    'send_tender_file' => null,
                ]);
                $record->notification()->delete();
                break;
            case '5':
                $record->update([
                    'status' => '4', 
                ]);
                Contract::where('equipment_apply_record_id', $record->id)->delete();
                $record->notification()->delete();
                break;
            case '6':
                $record->update([
                    'status' => '5',
                    'install_date' => null,
                    'install_picture' => null,
                ]);
                $record->notification()->delete();
                break;
            case '7':
                $record->update([
                    'status' => '6',
                    'isAdvance' => null,
                    'advance_status' => null,
                ]);
                $record->notification()->delete();
                break;
            case '8':
                $record->update([
                    'status' => '7',
                    'warehousing_date' => null,
                ]);
                $record->notification()->delete(); 
                break;
        }
        return new EquipmentApplyRecordResource($record);
    }
}
