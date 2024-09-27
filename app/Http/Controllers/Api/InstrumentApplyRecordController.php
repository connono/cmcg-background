<?php

namespace App\Http\Controllers\Api;

use App\Models\InstrumentApplyRecord;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InstrumentApplyRecordResource;
use App\Models\Notification;

class InstrumentApplyRecordController extends Controller
{
    public function index(Request $request, InstrumentApplyRecord $record){
        $query = $record->query();
        if (!is_null($request->serial_number)) {
            $query = $query->where('serial_number', 'like', '%'.$request->serial_number.'%');
        }
        if (!is_null($request->department)) {
            $department = Department::where('name', $request->department)->first();
            $query = $query->where('department', $department->label);
        }
        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
        }
        if (!is_null($request->type)) {
            $query = $query->where('type', $request->type);
        }
        if (!is_null($request->instrument)) {
            $query = $query->where('instrument', 'like', '%'.$request->instrument.'%');
        }
        if (!is_null($request->isAdvance)) {
            $query = $query->where('isAdvance', $request->isAdvance);
        }

        if (!is_null($request->isPaginate)) {
            $records = $query->paginate();
        } else {
            $records = $query->get();
        }
        
        return  InstrumentApplyRecordResource::collection($records);
    }

    public function getItem(Request $request, InstrumentApplyRecord $record){
        $record = InstrumentApplyRecord::find($request->id);
        return new InstrumentApplyRecordResource($record);
    }

    public function getSerialNumber(Request $request, InstrumentApplyRecord $record){
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
        while(\Cache::has('instrument_serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'instrument_serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['instrument_serial_number_'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
    }

    public function store(Request $request){
        if(!\Cache::has('instrument_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $department = Department::where('name', $request->department)->first();
        
        $record = InstrumentApplyRecord::create([
            'instrument' => $request->instrument,
            'count' => $request->count,
            'budget' => $request->budget,
            'apply_picture' => $request->apply_picture,
            'type' => $request->type,
        ]);

        $record->department = $department->label;
        $record->serial_number = $request->serial_number;
        $record->status = '1';
        $record->save();

        $notification = Notification::create([
            'permission' => 'can_survey_instrument',
            'title' => $record->instrument,
            'body' => json_encode($record, true),
            'category' => 'apply',
            'n_category' => 'instrumentApplyRecord',
            'type' => 'survey',
            'link' => '/apply/instrument/detail#update&' . $record->id,
        ]);

        $record->notification()->delete();
        $record->notification()->save($notification);

        \Cache::forget('instrument_serial_number_'.$request->serial_number);

        return new InstrumentApplyRecordResource($record);
    }

    public function update(Request $request, $method, InstrumentApplyRecord $record){
        switch($request->method){
            case 'survey':
                $attributes = $request->only(['survey_date','survey_picture']);
                $attributes['status'] = '2';
                $notification = Notification::create([
                    'permission' => 'can_contract_instrument',
                    'title' => $record->instrument,
                    'body' => json_encode($record, true),
                    'category' => 'apply',
                    'n_category' => 'instrumentApplyRecord',
                    'type' => 'contract',
                    'link' => '/apply/instrument/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'purchase':
                $attributes = $request->only(['price','purchase_picture']);
                $attributes['status'] = '3';
                $notification = Notification::create([
                    'permission' => 'can_install_instrument',
                    'title' => $record->instrument,
                    'body' => json_encode($record, true),
                    'category' => 'apply',
                    'n_category' => 'instrumentApplyRecord',
                    'type' => 'install',
                    'link' => '/apply/instrument/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'install':
                $attributes = $request->only(['install_date','install_picture']);
                $attributes['status'] = '4';
                $notification = Notification::create([
                    'permission' => 'can_engineer_approve_instrument',
                    'title' => $record->instrument,
                    'body' => json_encode($record, true),
                    'category' => 'apply',
                    'n_category' => 'instrumentApplyRecord',
                    'type' => 'engineer_approve',
                    'link' => '/apply/instrument/detail#update&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                break;
            case 'engineer_approve':
                $attributes = $request->only(['isAdvance']);
                $attributes['status'] = '5';
                $attributes['advance_status'] = '0';
                $record->notification()->delete();
                break;
        }
        $record->update($attributes);
        return new InstrumentApplyRecordResource($record);
    }

    public function delete(Request $request, InstrumentApplyRecord $record){
        $record->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function back(Request $request, InstrumentApplyRecord $record){
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
                    'survey_picture' => null,
                ]);
                break;
            case '3':
                $record->update([
                    'status' => '2',
                    'price' => null,
                    'purchase_picture' => null,
                ]);
                break;
            case '4':
                $record->update([
                    'status' => '3',
                    'install_date' => null,
                    'install_picture' => null,
                    'isAdvance' => null,
                    'advance_status' => null,
                ]);
                break;
        }
        return new InstrumentApplyRecordResource($record);
    }
}
