<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RepairApplyRecord;
use App\Models\Department;
use App\Http\Resources\RepairApplyRecordResource;

class RepairApplyRecordController extends Controller
{
    public function index(Request $request, RepairApplyRecord $record){
        $query = $record->query();
        if (!is_null($request->department)) {
            $department = Department::where('name', $request->department)->first();
            $query = $query->where('department', $department->label);
        }
        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
        }
        if (!is_null($request->isAdvance)) {
            $query = $query->where('isAdvance', $request->isAdvance);
        }
        if (!is_null($request->name)) {
            $query = $query->where('name', 'like', '%'.$request->name.'%');
        }
        if (!is_null($request->equipment)) {
            $query = $query->where('equipment', 'like', '%'.$request->equipment.'%');
        }
        $records = $query->paginate();
        
        return  RepairApplyRecordResource::collection($records);
    }

    public function getItem(Request $request, RepairApplyRecord $record){
        $record = RepairApplyRecord::find($request->id);
        return new RepairApplyRecordResource($record);
    }

    public function getSerialNumber(Request $request, RepairApplyRecord $record){
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
        while(\Cache::has('repair_serial_number_'.$serial_number)){
            $serial_number++;
        }
        $cacheKey = 'repair_serial_number_'.$serial_number;
        $expiredAt = now()->addMinutes(60);
        \Cache::put($cacheKey, ['repair_serial_number_'=>(string)$serial_number], $expiredAt);
        return response()->json([
            'serial_number' => (string)$serial_number,
            'expired_at' => $expiredAt->toDateTimeString(),
            'record_serial_number' => $record->serial_number,
        ])->setStatusCode(201);
        return $count;
    }

    public function store(Request $request){
        if(!\Cache::has('repair_serial_number_'.$request->serial_number)){
            return response()->json([
                'error' => 'false'
            ])->setStatusCode(500);
        }

        $department = Department::where('name', $request->department)->first();
        
        $record = RepairApplyRecord::create([
            'name' => $request->name,
            'budget' => $request->budget,
            'equipment' => $request->equipment,
            'apply_date' => $request->apply_date,
            'apply_file' => $request->apply_file,
        ]);

        $record->department = $department->label;
        $record->serial_number = $request->serial_number;
        $record->status = '1';
        $record->save();

        \Cache::forget('repair_serial_number_'.$request->serial_number);

        return new RepairApplyRecordResource($record);
    }

    public function update(Request $request, $method, RepairApplyRecord $record){
        switch($request->method){
            case 'install':
                $attributes = $request->only(['price','install_file','isAdvance']);
                $attributes['status'] = '2';
                $attributes['advance_status'] = '0';
                break;
        }
        $record->update($attributes);
        return new RepairApplyRecordResource($record);
    }

    public function delete(Request $request, RepairApplyRecord $record){
        $record->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function back(Request $request, RepairApplyRecord $record){
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
                    'price' => null,
                    'isAdvance' => null,
                    'install_file' => null,
                ]);
                break;
        }
        return new RepairApplyRecordResource($record);
    }
}
