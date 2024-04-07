<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentProcess;
use App\Models\PaymentProcessRecord;
use App\Http\Resources\PaymentProcessRecordResource;
use App\Models\Notification;
use App\Models\Contract;
use App\Models\EquipmentApplyRecord;

class PaymentProcessRecordsController extends Controller
{
    public function index(Request $request, PaymentProcess $process){
        $records = $process->records->diff(PaymentProcessRecord::whereNull('assessment_date')->get());
        
        return  PaymentProcessRecordResource::collection($records);
    }

    public function getItem(Request $request, PaymentProcessRecord $record){
        $record = PaymentProcessRecord::find($request->id);
        return new PaymentProcessRecordResource($record);
    }

    public function store(Request $request){
        $record = PaymentProcessRecord::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'next_date' => $request->next_date,
        ]);
        $record->save();

        $process = PaymentProcess::find($request->process_id);
        $process->records()->save($record);
        $process->update([
            'next_date' => $request->next_date,
            'current_payment_record_id' => $record->id,
            'status' => 'apply',
        ]);
        if($process->notification()) $process->notification()->delete();

        return new PaymentProcessRecordResource($record);
    }

    public function update(Request $request, PaymentProcessRecord $record){
        $process = PaymentProcess::find($request->process_id);
        switch($request->method) {
            case 'apply':
                $attributes = $request->only(['assessment']);
                $process->update([
                    'status' => 'document',
                    'assessment' => $request->assessment,
                ]);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $contract = Contract::find($process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
                $notification = Notification::create([
                    'permission' => 'can_document_payment_process_record',
                    'title' => $process->contract_name,
                    'body' => json_encode($information),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentProcess',
                    'type' => 'document',
                    'link' => '/purchase/paymentProcess/detail#document&' . $process->id . '&' . $process->current_payment_record_id,
                ]);
                $process->notification()->delete();
                $process->notification()->save($notification);
                break;
            case 'document':
                $attributes = $request->only(['payment_voucher_file']);
                $process->update([
                    'status' => 'finance_audit',
                ]);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $contract = Contract::find($process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
                $notification = Notification::create([
                    'permission' => 'can_finance_audit_payment_process_record',
                    'title' => $process->contract_name,
                    'body' => json_encode($information),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentProcess',
                    'type' => 'finance_audit',
                    'link' => '/purchase/paymentProcess/detail#finance_audit&' . $process->id . '&' . $process->current_payment_record_id,
                ]);
                $process->notification()->delete();
                $process->notification()->save($notification);
                break;
            case 'finance_audit':
                $attributes = [];
                $process->update(['status' => 'dean_audit']);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $contract = Contract::find($process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
                $notification = Notification::create([
                    'permission' => 'can_dean_audit_payment_process_record',
                    'title' => $process->contract_name,
                    'body' => json_encode($information),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentProcess',
                    'type' => 'dean_audit',
                    'link' => '/purchase/paymentProcess/detail#dean_audit&' . $process->id . '&' . $process->current_payment_record_id,
                ]);
                $process->notification()->delete();
                $process->notification()->save($notification);
                break;
            case 'dean_audit':
                $attributes = [];
                $process->update(['status' => 'process']);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $contract = Contract::find($process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
                $notification = Notification::create([
                    'permission' => 'can_process_payment_process_record',
                    'title' => $process->contract_name,
                    'body' => json_encode($information),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentProcess',
                    'type' => 'process',
                    'link' => '/purchase/paymentProcess/detail#process&' . $process->id . '&' . $process->current_payment_record_id,
                ]);
                $process->notification()->delete();
                $process->notification()->save($notification);
                break;
            case 'process':
                $attributes = $request->only(['assessment_date', 'payment_file']);
                $assessments_count = $process->assessments_count + $record->assessment;
                $records_count = $process->records_count + 1;
                if ($process->target_amount && $process->target_amount == $assessments_count){
                    $process->update([
                        'status' => 'stop',
                        'assessment' => null,
                        'next_date' => null,
                        'records_count' => $records_count,
                        'assessments_count' => $assessments_count,
                    ]);
                    break;
                }
                $process->update([
                    'status' => 'wait',
                    'assessment' => null,
                    'next_date' => null,
                    'records_count' => $records_count,
                    'assessments_count' => $assessments_count, 
                ]);
                $recordJSON = json_encode($record, true);
                $record_array = json_decode($recordJSON, true);
                $contract = Contract::find($process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                $processJSON = json_encode($process, true);
                $process_array = json_decode($processJSON, true);
                $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
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
                break;
            
        }
        $record->update($attributes);
        return new PaymentProcessRecordResource($record);
    }

    public function back(Request $request, PaymentProcessRecord $record) {
        $process = PaymentProcess::find($request->process_id);
        $process->update(['status' => 'apply']);
        $record->update([
            'assessment' => null,
            'payment_voucher_file' => null,
        ]);
        $recordJSON = json_encode($record, true);
        $record_array = json_decode($recordJSON, true);
        $contract = Contract::find($process->contract_id);
        $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
        $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
        $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
        $processJSON = json_encode($process, true);
        $process_array = json_decode($processJSON, true);
        $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
        $notification = Notification::create([
            'permission' => $process->department,
            'title' => $process->contract_name,
            'body' => json_encode($information),
            'category' => 'purchaseMonitor',
            'n_category' => 'paymentProcess',
            'type' => 'apply',
            'link' => '/purchase/paymentProcess/detail#apply&' . $process->id . '&' . $process ->current_payment_record_id,
        ]);
        $process->notification()->delete();
        $process->notification()->save($notification);
        return new PaymentProcessRecordResource($record);
    }

    public function delete(Request $request, PaymentProcessRecord $record){
        $process = PaymentProcess::find($request->process_id);
        $records_count = $process->records_count - 1;
        $assessments_count = $process->assessments_count - $record->assessment;
        $process->update([
            'next_date' => null,
            'assessment' => null,
            'records_count' => $records_count,
            'assessments_count' => $assessments_count, 
        ]);
        $record->delete();
        return response()->json([])->setStatusCode(200);
    }
}
