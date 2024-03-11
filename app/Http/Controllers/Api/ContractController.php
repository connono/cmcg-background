<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentPlanResource;
use App\Http\Resources\PaymentProcessResource;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use App\Http\Resources\ContractResource;
use App\Models\EquipmentApplyRecord;
use App\Models\InstrumentApplyRecord;
use App\Models\Notification;

class ContractController extends Controller
{
    public function index(Request $request, Contract $contract){
        $query = $contract->query();

        if (!is_null($request->contract_name)) {
            $query = $query->where('contract_name', 'like', '%'.$request->contract_name.'%');
        }

        if (!is_null($request->series_number)) {
            $query = $query->where('series_number', 'like', '%'.$request->series_number.'%');
        }

        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }

        if (!is_null($request->source)) {
            $query = $query->where('source', $request->source);
        }

        if (!is_null($request->isImportant)) {
            $query = $query->where('isImportant', $request->isImportant);
        }

        $contracts = $query->paginate();
        
        return  ContractResource::collection($contracts);
    }

    public function getItem(Request $request, Contract $contract){
        $contract = Contract::find($request->id);
        return new ContractResource($contract);
    }

    public function store(Request $request){
        $contract = Contract::create([
            'contract_name' => $request->contract_name,
            'category' => $request->category,
            'contractor' => $request->contractor,
            'source' => $request->source,
            'price' => $request->price,
            'contract_file' =>  $request->contract_file,
            'isImportant' => $request->isImportant,
            'comment' => $request->comment,
            'isComplement' => $request->isComplement,
        ]);
        $series_code = 0;
        $len = 5;
        $series_number = date('Y') . date('m') . $request->category . sprintf("%0{$len}d", $series_code);
        while(Contract::where('series_number', '=', $series_number)->exists()) {
            $series_code++;
            $series_number = date('Y') . date('m') . $request->category . sprintf("%0{$len}d", $series_code);
        }
        $contract->series_number = $series_number;
        $contract->save();
        if ($request->equipment_apply_record_id) {
            $equipment_apply_record = EquipmentApplyRecord::find($request->equipment_apply_record_id);
            $equipment_apply_record->contract()->save($contract);
            $equipment_apply_record->update([
                'price' => $contract->price,
                'purchase_picture' => $contract->contract_file,
                'status' => '5'
            ]);
            $department = Department::where('label', $equipment_apply_record->department)->first();
            $engineer_id = $department->engineer_id;
            $user = User::where('engineer_id', $engineer_id)->first();
            $notification = Notification::create([
                'permission' => 'can_install_equipment',
                'title' => $equipment_apply_record->equipment,
                'body' => json_encode($equipment_apply_record),
                'category' => 'apply',
                'n_category' => 'equipmentApplyRecord',
                'type' => 'install',
                'link' => '/apply/equipment/detail#update&' . $equipment_apply_record->id,
                'user_id' => $user->id,
            ]);
            $equipment_apply_record->notification()->delete();
            $equipment_apply_record->notification()->save($notification);
        }
        if ($request->instrument_apply_record_id) {
            $instrument_apply_record = InstrumentApplyRecord::find($request->instrument_apply_record_id);
            $instrument_apply_record->contract()->save($contract);
            $instrument_apply_record->update([
                'status' => '3'
            ]);
        }
        // $manager = User::find($request->manager_id);
        // $contract->manager()->save($manager);
        // $manage_dean = User::find($request->manage_dean_id);
        // $contract->manage_dean()->save($manage_dean);
        // $dean = User::find($request->dean_id);
        // $contract->dean()->save($dean);

        return new ContractResource($contract);
    }

    public function storeDocx(Request $request, Contract $contract){
        $attributes = $request->only(['contract_docx']);
        $contract->update($attributes);
        return new ContractResource($contract);
    }

    public function plans(Request $request, Contract $contract) {
        $plans = $contract->plans()->get();
        return PaymentPlanResource::collection($plans);
    }

    public function deletePlan(Request $request, Contract $contract) {
        $contract->plans()->where('id', $request->plan_id)->delete();
        return response()->json([])->setStatusCode(200);
    }

    public function deleteProcess(Request $request, Contract $contract) {
        $contract->processes()->where('id', $request->process_id)->delete();
        return response()->json([])->setStatusCode(200);
    }

    public function processes(Request $request, Contract $contract) {
        $processes = $contract->processes()->get();
        return PaymentProcessResource::collection($processes);
    }
    
    public function delete(Request $request, Contract $contract) {
        $contract->delete();
        return response()->json([])->setStatusCode(200);
    }
}
