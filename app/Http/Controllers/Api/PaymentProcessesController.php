<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentApplyRecord;
use Illuminate\Http\Request;
use App\Models\PaymentProcess;
use App\Models\Contract;
use App\Models\Notification;
use App\Http\Resources\PaymentProcessResource;

class PaymentProcessesController extends Controller
{
    public function index(Request $request, PaymentProcess $process){
        $query = $process->query();
        if ($request->department && $request->department !== '财务科' && $request->department !== '院长室') {
            $query = $query->where('department', $request->department);
        }
        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
        if ($request->contract_name) {
            $query = $query->where('contract_name', 'like', '%'.$request->contract_name.'%');
        }
        if (!is_null($request->isPaginate)) {
            $processes = $query->paginate();
        } else {
            $processes = $query->get();
        }
        return  PaymentProcessResource::collection($processes);
    }

    public function getItem(Request $request, PaymentProcess $process){
        $process = PaymentProcess::find($request->id);
        return new PaymentProcessResource($process);
    }

    public function store(Request $request){
        $process = PaymentProcess::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'is_pay' => $request->is_pay,
            'category' => $request->category,
            'contract_date' => $request->contract_date,
            'payment_file' => $request->payment_file,
        ]);
        $process->target_amount = $request->target_amount;
        $process->records_count = 0;
        $process->assessments_count = 0;
        $process->status = 'wait';
        $process->save();

        if ($request->contract_id) {
            $contract = Contract::find($request->contract_id);
            $contract_name = $contract->contract_name . $contract->series_number . '-' . $process->id;
            $process->update([
                'contract_name' => $contract_name,
            ]);
            $contract->processes()->save($process);
            $processJSON = json_encode($process, true);
            $process_array = json_decode($processJSON, true);
            if(!is_null($contract->equipment_apply_record_id)) {
                $record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $record->notification()->delete(); 
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $information = (object) array_merge($record_array, $process_array);   
            } else {
                $information = (object) $process_array;
            }
            $notification = Notification::create([
                'permission' => $process->department,
                'title' => $process->contract_name,
                'body' => json_encode($information),
                'category' => 'purchaseMonitor',
                'n_category' => 'paymentProcess',
                'type' => 'wait',
                'link' => '/purchase/paymentProcess',
            ]);
            $process->notification()->delete();
            $process->notification()->save($notification);
        }

        return new PaymentProcessResource($process);
    }

    public function stop(Request $request, PaymentProcess $process) {
        $process->update([
            'status' => 'stop'
        ]);
        $process->notification()->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function delete(Request $request, PaymentProcess $process) {
        foreach($process->records as $record){
            $record->delete();
        }
        $process->delete();
        return response()->json([])->setStatusCode(200);
    }
}
