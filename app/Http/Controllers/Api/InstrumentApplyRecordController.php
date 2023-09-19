<?php

namespace App\Http\Controllers\Api;

use App\Models\InstrumentApplyRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\InstrumentApplyRecordResource;
use App\Http\Requests\Api\InstrumentApplyRecordRequest;

class InstrumentApplyRecordController extends Controller
{
    public function index(Request $request, InstrumentApplyRecord $record){
        $query = $record->query();
        $records = $query->paginate();
        
        return  InstrumentApplyRecordResource::collection($records);
    }

    public function store(InstrumentApplyRecordRequest $request){
        $record = InstrumentApplyRecord::create([
            'status' => $request->status,
            'equipment' => $request->equipment,
            'department' => $request->department,
            'count' => $request->count,
            'budget' => $request->budget,
        ]);
        
        $record->serial_number = $request->serial_number;
        $record->save();

        return new InstrumentApplyRecordResource($record);
    }

    public function update(InstrumentApplyRecordRequest $request, $method, InstrumentApplyRecord $record){
        switch($request->method){
            case 'survey':
                $attributes = $request->only(['survey_date']);
                $attributes['status'] = 1;
                break;
            case 'purchase':
                $attributes = $request->only(['price']);
                $attributes['status'] = 2;
                break;
            case 'install':
                $attributes = $request->only(['install_date']);
                $attributes['status'] = 3;
                break;
        }
        $record->update($attributes);
        return new InstrumentApplyRecordResource($record);
    }
}
