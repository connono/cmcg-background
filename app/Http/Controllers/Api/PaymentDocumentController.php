<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PaymentDocument;
use App\Http\Resources\PaymentDocumentResource;
use App\Models\ApprovalRecord;
use App\Models\PaymentProcessRecord;
use App\Models\PaymentProcess;
use App\Models\Contract;
use App\Models\EquipmentApplyRecord;
use App\Models\InstrumentApplyRecord;
use App\Models\Notification;
use App\Models\User;
use App\Models\Leader;
use Faker\Provider\ar_EG\Payment;

class PaymentDocumentController extends Controller
{

    public function index(Request $request, PaymentDocument $record){
        $query = $record->query();

        if($request->department !== '财务科' && $request->department !== '院长室'){
            $query = $query->where('department', $request->department);
        }

        if (!is_null($request->status)) {
            $query = $query->where('status', $request->status);
        }
        if (!is_null($request->id)) {
            $query = $query->where('id', $request->id);
        }

        $records = $query->paginate();
    
        return  PaymentDocumentResource::collection($records);
    }
    
    public function getItem(Request $request, PaymentDocument $record) {
        return new PaymentDocumentResource($record);
    }

    public function item(Request $request, PaymentDocument $record) {
        $payment_process_records = PaymentProcessRecord::where('payment_document_id', $record->id)->get();
        $data = array();
        foreach($payment_process_records as $payment_process_record) {
            $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
            $last_payment_process_record = PaymentProcessRecord::where('payment_process_id', $payment_process->id)->whereNotNull('payment_date')->first();                
            $contract = Contract::find($payment_process->contract_id);
            $equipment_apply_record = EquipmentApplyRecord::where('contract_id', $contract->id)->first();
            $text = $payment_process->assessments_count == 0 ? '首款' : '尾款';
            
            array_push($data, [
                'status' => $record->status,
                'company' => $contract->contractor,
                'equipment' => $equipment_apply_record ? $equipment_apply_record->equipment : $contract->contract_name,
                'price' => $payment_process->target_amount,
                'type' => $text,
                'last_pay_date' => is_null($last_payment_process_record) ? '' : $last_payment_process_record->payment_date,
                'assessments_count' => $payment_process->assessments_count,
                'assessment' => $payment_process_record->assessment,
                'rest_money' => $payment_process->target_amount - $payment_process->assessments_count - $payment_process_record->assessment,
                'payment_terms_now' => $payment_process_record->payment_terms,
                'payment_terms' => $contract->payment_terms,
                'contract_file' => $contract->contract_file,
                'install_picture' => $equipment_apply_record ? $equipment_apply_record->install_picture : $payment_process->install_picture,
            ]);
        }
        return response()->json([
            'data' => $data,
            'id' =>  $record->id,
        ]);
    }

    public function store(Request $request){
        $department = Department::where('label', $request->department)->first();
        $leader = Leader::find($department->leader_id);
        $user = User::where('name', $leader->name)->first();

        $record = PaymentDocument::create([
            'create_date' => $request->create_date,
            'all_price' => $request->all_price,
            'status' => 'dean_audit',
            'department' => $request->department,
            'user_id' => $request->user_id,
        ]);
        $record->save();
        ApprovalRecord::create([
            'user_id' => $request->user_id,
            'approve_date' => date('Y-m-d H:i:s'),
            'approve_model_id' => $record->id, 	
            'approve_model' => 'PaymentDocument', 	
            'approve_status' => 'apply',
        ]);
        $data = array();
        if($request->serial_process_record_ids) {
            $payment_process_record_ids = explode("&", $request->serial_process_record_ids);
            foreach ($payment_process_record_ids as $payment_process_record_id) {
                $payment_process_record = PaymentProcessRecord::find($payment_process_record_id);
                $payment_process_record->update([
                    'payment_document_id' => $record->id,
                ]);
                $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                $last_payment_process_record = PaymentProcessRecord::where('payment_process_id', $payment_process->id)->whereNotNull('payment_date')->first();                
                $contract = Contract::find($payment_process->contract_id);
                $equipment_apply_record = EquipmentApplyRecord::where('contract_id', $contract->id)->first();

                $text = $payment_process->assessments_count == 0 ? '首款' : '尾款';
                $payment_process->notification()->delete();
                array_push($data, [
                    'company' => $contract->contractor,
                    'equipment' => $equipment_apply_record ? $equipment_apply_record->equipment : $contract->contract_name,
                    'price' => $payment_process->target_amount,
                    'type' => $text,
                    'last_pay_date' => is_null($last_payment_process_record) ? '' : $last_payment_process_record->payment_date,
                    'assessments_count' => $payment_process->assessments_count,
                    'assessment' => $payment_process_record->assessment,
                    'rest_money' => $payment_process->target_amount - $payment_process->assessments_count - $payment_process_record->assessment,
                    'payment_terms_now' => $payment_process_record->payment_terms,
                    'payment_terms' => $contract->payment_terms,
                ]);
            }
        }
        $today = date('Y年m月d日');
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $today . "制单". "科室：" . $request->department,
            'body' => json_encode($record),
            'category' => 'purchaseMonitor',
            'n_category' => 'paymentDocument',
            'type' => 'dean_audit',
            'link' => '/purchase/paymentDocument/detail#dean_audit&' . $record->id,
        ]);
        $record->notification()->save($notification);
        return response()->json([
            'data' => $data,
            // 'signature' => $signatures,
            'id' =>  $record->id,
        ]);
    }


    public function reject(Request $request, PaymentDocument $record)
    {
        $payment_process_records = PaymentProcessRecord::where('payment_document_id', $record->id)->get();
        $user = User::find($request->user_id);
        $record->update([
            'status' => 'reject',
            'reject_reason' => $user->name . ":" . $request->reject_reason,
        ]);
        foreach ( $payment_process_records as $payment_process_record) {
            $payment_process_record->update([
                'payment_document_id' => null,
            ]);
            $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
            $payment_process->update([
                'status' => 'apply',
            ]);
        }
        ApprovalRecord::create([
            'user_id' => $request->user_id,
            'approve_date' => date('Y-m-d H:i:s'),
            'approve_model_id' => $record->id, 	
            'approve_model' => 'PaymentDocument', 	
            'approve_status' => 'reject',
            'reject_reason' => is_null($request->reject_reason) ? '': $request->reject_reason,
        ]);
        $record->notification()->delete();
        return response()->json([])->setStatusCode(200);
    }

    public function storeXlsx(Request $request, PaymentDocument $record){
        $attributes = $request->only(['excel_url']);
        $record->update($attributes);
        return new PaymentDocumentResource($record);
    }

    public function update(Request $request, PaymentDocument $record) {
        $payment_process_records = PaymentProcessRecord::where('payment_document_id', $record->id)->get();
        date_default_timezone_set('Asia/Shanghai');
        switch ($record->status) {
            case 'dean_audit':
                $record->update([
                    'status' => 'audit',
                ]);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'audit']);
                }
                ApprovalRecord::create([
                    'user_id' => $request->user_id,
                    'approve_date' => date('Y-m-d H:i:s'),
                    'approve_model_id' => $record->id, 	
                    'approve_model' => 'PaymentDocument', 	
                    'approve_status' => 'dean_audit',
                ]);
                $old_notification = $record->notification()->first();
                $notification = Notification::create([
                    'permission'=> 'can_audit_payment_document',
                    'title' => $old_notification->title,
                    'body' => json_encode($record),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentDocument',
                    'type' => 'audit',
                    'link' => '/purchase/paymentDocument/detail#audit&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                return response()->json([])->setStatusCode(200); 
            case 'audit':
                $record->update([
                    'status' => 'finance_audit',
                ]);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'finance_audit']);
                }
                ApprovalRecord::create([
                    'user_id' => $request->user_id,
                    'approve_date' => date('Y-m-d H:i:s'),
                    'approve_model_id' => $record->id, 	
                    'approve_model' => 'PaymentDocument', 	
                    'approve_status' => 'audit', 
                ]);
                $old_notification = $record->notification()->first();
                $notification = Notification::create([
                    'permission'=> 'can_finance_audit_payment_document',
                    'title' => $old_notification->title,
                    'body' => json_encode($record),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentDocument',
                    'type' => 'finance_audit',
                    'link' => '/purchase/paymentDocument/detail#finance_audit&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                return response()->json([])->setStatusCode(200); 
            case 'finance_audit':
                $record->update([
                    'status' => 'finance_dean_audit',
                ]);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'finance_dean_audit']);
                }
                ApprovalRecord::create([
                    'user_id' => $request->user_id,
                    'approve_date' => date('Y-m-d H:i:s'),
                    'approve_model_id' => $record->id, 	
                    'approve_model' => 'PaymentDocument', 	
                    'approve_status' => 'finance_audit', 
                ]);
                $payment_process_record = PaymentProcessRecord::where('payment_document_id', $record->id)->first();
                $department = Department::where('label', $payment_process_record->department)->first();
                $leader = Leader::find($department->leader_id);
                $old_notification = $record->notification()->first();
                $user = User::where('name', $leader->name)->first();
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $old_notification->title,
                    'body' => json_encode($record),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentDocument',
                    'type' => 'finance_dean_audit',
                    'link' => '/purchase/paymentDocument/detail#audit&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                return response()->json([])->setStatusCode(200); 
            case 'finance_dean_audit':
                $record->update(['status' => 'upload']);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'upload']);
                }
                ApprovalRecord::create([
                    'user_id' => $request->user_id,
                    'approve_date' => date('Y-m-d H:i:s'),
                    'approve_model_id' => $record->id, 	
                    'approve_model' => 'PaymentDocument', 	
                    'approve_status' => 'finance_dean_audit', 
                ]);
                $old_notification = $record->notification()->first();
                $notification = Notification::create([
                    'user_id' => $record->user_id_1,
                    'title' => $old_notification->title,
                    'body' => json_encode($record),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentDocument',
                    'type' => 'upload',
                    'link' => '/purchase/paymentDocument/detail#upload&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                return response()->json([])->setStatusCode(200); 
            case 'upload':
                $record->update([
                    'status' => 'finish',
                    'payment_document_file' => $request->payment_document_file,
                ]);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'process']);
                    $recordJSON = json_encode($payment_process_record, true);
                    $record_array = json_decode($recordJSON, true);
                    $contract = Contract::find($payment_process->contract_id);
                    $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                    $processJSON = json_encode($payment_process, true);
                    $process_array = json_decode($processJSON, true);
                    $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                    $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                    if (!is_null($equipment_apply_record_array)) {
                        $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);    
                    } else {
                        $information = (object) array_merge($record_array, $process_array);    
                    }
                    $notification = Notification::create([
                        'permission' => 'can_process_payment_process_record',
                        'title' => $payment_process->contract_name,
                        'body' => json_encode($information),
                        'category' => 'purchaseMonitor',
                        'n_category' => 'paymentProcess',
                        'type' => 'process',
                        'link' => '/purchase/paymentProcess/detail#process&' . $payment_process->id . '&' . $payment_process->current_payment_record_id,
                    ]);
                    $payment_process->notification()->delete();
                    $payment_process->notification()->save($notification);
                }
                $record->notification()->delete();
                return response()->json([])->setStatusCode(200); 

        }

    }

    public function delete(Request $request, PaymentDocument $record){
        $payment_process_records = $record->paymentProcessRecords()->get();
        foreach ( $payment_process_records as $payment_process_record) {
            $payment_process_record->update([
                'payment_document_id' => null,
                'status' => 'apply',
            ]);
        }
        $record->delete();
        return response()->json([])->setStatusCode(200);
    }
}
