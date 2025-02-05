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

        $user = User::find($request->user_id);
        $department = Department::find($user->department_id);
        $query = $contract->query();
        $all_department_source = ['ALL', 'CG', 'CW', 'YB'];
        if(is_null($department->acronym)) 
            return response()->json([])->setStatusCode(200);
        if(!in_array($department->acronym, $all_department_source)){
            $query = $query->where('department_source', $department->acronym);
        }

        if ($request->department_source) {
            $query = $query->where('department_source', $request->department_source);
        }

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

        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
        }

        if (!is_null($request->created_at)) {
            $start = \Carbon\Carbon::parse($request->created_at[0]);
            $end = \Carbon\Carbon::parse($request->created_at[1]);
            $query = $query->whereBetween('created_at', [$start, $end]);
        }

        if (!is_null($request->isPaginate)) {
            $contracts = $query->paginate();
        } else {
            $contracts = $query->get();
        }
        
        return  ContractResource::collection($contracts);
    }

    public function getItem(Request $request, Contract $contract){
        $contract = Contract::find($request->id);
        return new ContractResource($contract);
    }

    public function store(Request $request){
        $contract = Contract::create([
            'contract_name' => $request->contract_name,
            'type' => $request->type,
            'complement_code' => $request->complement_code,
            'category' => $request->category,
            'contractor' => $request->contractor,
            'department_source' => $request->department_source,
            'source' => $request->source,
            'purchase_type' => $request->purchase_type,
            'price' => $request->price,
            'isImportant' => $request->isImportant,
            'dean_type' => $request->dean_type,
            'law_advice' => $request->law_advice,
            'comment' => $request->comment,
            'is_pay' => $request->is_pay,
            'isComplement' => $request->isComplement,
            'payment_terms' => $request->payment_terms,
            'status' => 'approve',
        ]);
        $series_code = 1;
        $series_number = $request->department_source . date('Y'). '0' . $series_code;
        while(Contract::where('series_number', '=', $series_number)->exists()) {
            $series_code++;
            if ($series_code >= 100) {
                $series_number = $request->department_source . date('Y'). $series_code;
            } else {
                $series_number = $request->department_source . date('Y'). '0' . $series_code;
            }
        }
        $contract->series_number = $series_number;
        $contract->save();
        $department = Department::where('acronym', $request->department_source)->first();
        $notification = Notification::create([
            'permission' => 'can_approve_contracts',
            'title' => $contract->contract_name,
            'body' => json_encode($contract),
            'category' => 'purchaseMonitor',
            'n_category' => 'contract',
            'type' => 'approve',
            'link' => '/purchase/contract/detail#' . $contract->id,
        ]);
        $contract->notification()->save($notification);
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

    public function addEquipmentApplyRecord(Request $request, Contract $contract) {
        $equipment_apply_record = EquipmentApplyRecord::find($request->equipment_apply_record_id);
        $equipment_apply_record->update([
            'price' => $contract->price,
            'purchase_picture' => $contract->contract_file,
            'contract_id' => $contract->id,
            'status' => '5'
        ]);
        $department_string_array = explode(",", $equipment_apply_record->department);
        $department = Department::where('label', $department_string_array[0])->first();
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
        return new ContractResource($contract);
    }

    public function update(Request $request, Contract $contract){
        switch($request->method){
            case 'approve':
                $attributes = ['status' => 'upload'];
                $contract->update($attributes);
                $department = Department::where('acronym', $contract->department_source)->first();
                $notification = Notification::create([
                    'permission' => 'can_create_payment_process',
                    'title' => $contract->contract_name,
                    'body' => json_encode($contract),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'contract',
                    'type' => 'upload',
                    'link' => '/purchase/contract/detail#' . $contract->id,
                    'department_id' => $department->id,
                ]);
                $contract->notification()->delete();
                $contract->notification()->save($notification);
                break;
            case 'upload':
                $attributes = $request->only(['contract_file']);
                $attributes['status'] = 'finish';
                $contract->update($attributes);
                $contract->notification()->delete();
                break;
        }
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
        $department = Department::where('acronym', $contract->department_source)->first();
        $notification = Notification::create([
            'permission' => 'can_create_payment_process',
            'title' => $contract->contract_name,
            'body' => json_encode($contract),
            'category' => 'purchaseMonitor',
            'n_category' => 'contract',
            'type' => 'delete',
            'link' => '/purchase/contract#create',
            'department_id' => $department->id,
        ]);
        $notification->link = '/purchase/contract#create&' . $notification->id;
        $notification->save();
        $contract->notification()->delete();
        $contract->delete();
        return response()->json([])->setStatusCode(200);
    }
}
