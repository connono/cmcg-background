<?php

namespace App\Http\Controllers\Api;

use App\Models\EquipmentApplyRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentApplyRecordResource;
use App\Http\Requests\Api\EquipmentApplyRecordRequest;
use Illuminate\Support\Str;
use App\Handlers\ImageUploadHandler;
use Illuminate\Support\Facades\Storage;

class EquipmentApplyRecordController extends Controller
{

    public function index(Request $request, EquipmentApplyRecord $record){
        $query = $record->query();
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
        while(\Cache::has('serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['serial_number'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
        return $count;
    }

    public function store(EquipmentApplyRecordRequest $request, ImageUploadHandler $uploader){
        if(!\Cache::has('serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $record = EquipmentApplyRecord::create([
            'equipment' => $request->equipment,
            'department' => $request->department,
            'count' => $request->count,
            'budget' => $request->budget,
            'apply_type' => $request->apply_type,
        ]);
        
        $record->serial_number = $request->serial_number;
        $record->status = '1';

        if ($request->apply_picture) {
            $result = $uploader->save($request->apply_picture, 'apply', $request->serial_number);
            if ($result) {
                $record->apply_picture = $result['path'];
            }
        }

        $record->save();

        \Cache::forget('serial_number_'.$request->serial_number);

        return new EquipmentApplyRecordResource($record);
    }

    public function update(EquipmentApplyRecordRequest $request, $method, EquipmentApplyRecord $record, ImageUploadHandler $uploader){
        switch($request->method){
            case 'survey':
                $attributes = $request->only(['survey_date','purchase_type','survey_record','meeting_record']);
                if ($request->survey_picture) {
                    $result = $uploader->save($request->survey_picture, 'survey', $request->serial_number);
                    if ($result) {
                        $attributes['survey_picture'] = $result['path'];
                    }
                }
                $attributes['status'] = '2';
                break;
            case 'approve':
                $attributes = $request->only(['approve_date','execute_date']);
                if ($request->approve_picture) {
                    $result = $uploader->save($request->approve_picture, 'approve', $request->serial_number);
                    if ($result) {
                        $attributes['approve_picture'] = $result['path'];
                    }
                }
                $attributes['status'] = $record->purchase_type == 1 ? '3' : '4';
                break;
            case 'tender':
                $attributes = $request->only(['tender_date','tender_out_date']);
                if ($request->tender_file) {
                    $result = $uploader->save($request->tender_file, 'tender_file', $request->serial_number);
                    if ($result) {
                        $attributes['tender_file'] = $result['path'];
                    }
                }
                if ($request->tender_boardcast_file) {
                    $result = $uploader->save($request->tender_boardcast_file, 'tender_boardcast_file', $request->serial_number);
                    if ($result) {
                        $attributes['tender_boardcast_file'] = $result['path'];
                    }
                }
                if ($request->bid_winning_file) {
                    $result = $uploader->save($request->bid_winning_file, 'bid_winning_file', $request->serial_number);
                    if ($result) {
                        $attributes['bid_winning_file'] = $result['path'];
                    }
                }
                if ($request->send_tender_file) {
                    $result = $uploader->save($request->send_tender_file, 'send_tender_file', $request->serial_number);
                    if ($result) {
                        $attributes['send_tender_file'] = $result['path'];
                    }
                }
                $attributes['status'] = '4';
                break;
            case 'purchase':
                $attributes = $request->only(['purchase_date','arrive_date','price']);
                if ($request->purchase_picture) {
                    $result = $uploader->save($request->purchase_picture, 'purchase', $request->serial_number);
                    if ($result) {
                        $attributes['purchase_picture'] = $result['path'];
                    }
                }
                $attributes['status'] = '5';
                break;
            case 'install':
                $attributes = $request->only(['install_date']);
                if ($request->install_picture) {
                    $result = $uploader->save($request->install_picture, 'survey', $request->serial_number);
                    if ($result) {
                        $attributes['install_picture'] = $result['path'];
                    }
                }
                $attributes['status'] = '6';
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
                ]);
                break;
        }
        return new EquipmentApplyRecordResource($record);
    }
}
