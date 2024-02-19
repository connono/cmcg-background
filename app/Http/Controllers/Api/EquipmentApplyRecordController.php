<?php

namespace App\Http\Controllers\Api;

use App\Models\EquipmentApplyRecord;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentApplyRecordResource;
use App\Http\Requests\Api\EquipmentApplyRecordRequest;

class EquipmentApplyRecordController extends Controller
{

    public function index(Request $request, EquipmentApplyRecord $record){
        $query = $record->query();
        if (!is_null($request->department)) {
            $department = Department::where('name', $request->department)->first();
            $query = $query->where('department', $department->label);
        }
        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
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
        $records = $query->paginate();
        
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

        $department = Department::where('name', $request->department)->first();

        $record = EquipmentApplyRecord::create([
            'equipment' => $request->equipment,
            'count' => $request->count,
            'budget' => $request->budget,
            'apply_type' => $request->apply_type,
            'apply_picture' => $request->apply_picture,
        ]);
        $record->department = $department->label;
        $record->serial_number = $request->serial_number;
        $record->status = '1';

        $record->save();

        \Cache::forget('equipment_serial_number_'.$request->serial_number);

        return new EquipmentApplyRecordResource($record);
    }

    public function update(EquipmentApplyRecordRequest $request, $method, EquipmentApplyRecord $record){
        switch($request->method){
            case 'survey':
                $attributes = $request->only(['survey_date','purchase_type','survey_record','meeting_record', 'survey_picture']);
                $attributes['status'] = '2';
                break;
            case 'approve':
                $attributes = $request->only(['approve_date','execute_date', 'approve_picture']);
                $attributes['status'] = $record->purchase_type == 1 ? '3' : '4';
                break;
            case 'tender':
                $attributes = $request->only(['tender_date','tender_out_date', 'tender_file', 'tender_boardcast_file', 'bid_winning_file', 'send_tender_file']);
                $attributes['status'] = '4';
                break;
            case 'purchase':
                $attributes = $request->only(['purchase_date','arrive_date','price', 'purchase_picture']);
                $attributes['status'] = '5';
                break;
            case 'install':
                $attributes = $request->only(['install_date', 'install_picture', 'isAdvance']);
                $attributes['status'] = '6';
                $attributes['advance_status'] = '0';
                break;
            case 'warehouse':
                $attributes = $request->only(['warehousing_date']);
                $attributes['status'] = '7';
                break;
        }
        $record->update($attributes);
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
                break;
            case '3':
                $record->update([
                    'status' => '2',
                    'approve_date' => null,
                    'execute_date' => null,
                    'approve_picture' => null,
                ]);
                break;
            case '4':
                $record->update([
                    'status' => '3',
                    'tender_date' => null,
                    'tender_file' => null,
                    'tender_boardcast_file' => null,
                    'tender_out_date' => null,
                    'bid_winning_file' => null,
                    'send_tender_file' => null,
                ]);
                break;
            case '5':
                $record->update([
                    'status' => $record->purchase_type == '1' ? '4' : '3', 
                    'purchase_date' => null,
                    'arrive_date' => null,
                    'price' => null,
                    'purchase_picture' => null,
                ]);
                break;
            case '6':
                $record->update([
                    'status' => '5',
                    'install_date' => null,
                    'install_picture' => null,
                    'isAdvance' => null,
                    'advance_status' => null,
                ]);
                break;
        }
        return new EquipmentApplyRecordResource($record);
    }
}
