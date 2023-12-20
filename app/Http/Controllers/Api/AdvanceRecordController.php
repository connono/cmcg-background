<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvanceRecord;
use App\Models\EquipmentApplyRecord;
use App\Models\InstrumentApplyRecord;
use App\Models\RepairApplyRecord;
use App\Http\Resources\AdvanceRecordResource;

class AdvanceRecordController extends Controller
{
    public function index(Request $request, AdvanceRecord $record){
        $query = $record->query();

        if (!is_null($request->id)) {
            $query = $query->where('id', $request->id);
        }

        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
        }

        $records = $query->paginate();

        return  AdvanceRecordResource::collection($records);
    }

    public function getItem(Request $request, AdvanceRecord $record){
        if(is_null($request->id) || $request->id == 'undefined') return [];
        $record = AdvanceRecord::find($request->id)->with(['repairApplyRecords','instrumentApplyRecords','equipmentApplyRecords'])->get();
        return new AdvanceRecordResource($record);
    }

    public function storeAdvanceBudget(Request $request) {
        \Cache::put('advance_budget', $request->advance_budget);
        $advance_budget = \Cache::get('advance_budget');
        return response()->json([
            'advance_budget'=> $advance_budget,
        ])->setStatusCode(200);
    }

    public function getAdvanceBudget(Request $request) {
        $all_prices = AdvanceRecord::where('status', '1')->sum('all_price');
        if(!\Cache::has('advance_budget')) 
            return response()->json(['all_prices' => $all_prices, 'advance_budget' => 0])->setStatusCode(200);
        else {
            $advance_budget = \Cache::get('advance_budget');
            return response()->json(['all_prices' => $all_prices, 'advance_budget' => $advance_budget])->setStatusCode(200);
        }
    }

    public function store(Request $request){
        $record = AdvanceRecord::create([
            'create_date' => $request->create_date,
            'all_price' => $request->all_price,
            'status' => '1',
        ]);
        $record->save();
        if($request->serial_equipment_ids) {
            $equipment_ids = explode("&", $request->serial_equipment_ids);
            foreach ($equipment_ids as $equipment_id) {
                $equipment_apply_record = EquipmentApplyRecord::find($equipment_id);
                $equipment_apply_record->update([
                    'advance_status' => '1',
                ]);
                $record->equipmentApplyRecords()->save($equipment_apply_record);
            }
        }
        if($request->serial_instrument_ids) {
            $instrument_ids = explode("&", $request->serial_instrument_ids);
            foreach ($instrument_ids as $instrument_id) {
                $instrument_apply_record = InstrumentApplyRecord::find($instrument_id);
                $instrument_apply_record->update([
                    'advance_status' => '1',
                ]);
                $record->instrumentApplyRecords()->save($instrument_apply_record);
            }
        }
        if($request->serial_maintain_ids) {
            $maintain_ids = explode("&", $request->serial_maintain_ids);
            foreach ($maintain_ids as $maintain_id) {
                $repair_apply_record = RepairApplyRecord::find($maintain_id);
                $repair_apply_record->update([
                    'advance_status' => '1',
                ]);
                $record->repairApplyRecords()->save($repair_apply_record);
            }
        }
        return new AdvanceRecordResource($record);
    }

    public function update(Request $request, AdvanceRecord $record){
        $attributes = $request->only(['payback_date']);
        $attributes['status'] = '2';
        $equipment_apply_records = $record->equipmentApplyRecords()->get();
        foreach ( $equipment_apply_records as $equipment_apply_record) {
            $equipment_apply_record->update([
                'advance_status' => '2',
            ]);
        }
        $instrument_apply_records = $record->instrumentApplyRecords()->get();
        foreach ( $instrument_apply_records as $instrument_apply_record) {
            $instrument_apply_record->update([
                'advance_status' => '2',
            ]);
        }
        $repair_apply_records = $record->repairApplyRecords()->get();
        foreach ( $repair_apply_records as $repair_apply_record) {
            $repair_apply_record->update([
                'advance_status' => '2',
            ]);
        }
        $record->update($attributes);
        return new AdvanceRecordResource($record);
    }

    public function delete(Request $request, AdvanceRecord $record){
        $record->delete();
        return response()->json([])->setStatusCode(201);
    }
}
