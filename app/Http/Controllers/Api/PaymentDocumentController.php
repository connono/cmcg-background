<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PaymentDocument;
use App\Http\Resources\PaymentDocumentResource;
use App\Models\PaymentProcessRecord;
use App\Models\PaymentProcess;
use App\Models\Contract;
use App\Models\EquipmentApplyRecord;
use App\Models\InstrumentApplyRecord;
use App\Models\Notification;
use App\Models\User;
use App\Models\Leader;


class PaymentDocumentController extends Controller
{

    public function index(Request $request, PaymentDocument $record){
        $query = $record->query();

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
            if ($contract->equipment_apply_record_id) {
                $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
            }

            $text = $payment_process->assessments_count == 0 ? '首款' : '尾款';
            
            array_push($data, [
                'status' => $record->status,
                'company' => $contract->contractor,
                'equipment' => $contract->equipment_apply_record_id ? $equipment_apply_record->equipment : '',
                'price' => $contract->price,
                'type' => $text,
                'last_pay_date' => is_null($last_payment_process_record) ? '' : $last_payment_process_record->payment_date,
                'assessments_count' => $payment_process->assessments_count,
                'assessment' => $payment_process_record->assessment,
                'rest_money' => $contract->price - $payment_process->assessments_count - $payment_process_record->assessment,
                'payment_terms_now' => $payment_process_record->payment_terms,
                'payment_terms' => $contract->payment_terms,
                'contract_file' => $contract->contract_file,
                'install_picture' => $contract->equipment_apply_record_id ? $equipment_apply_record->install_picture : '',
            ]);
        }
        return response()->json([
            'data' => $data,
            'id' =>  $record->id,
        ]);
    }

    public function store(Request $request){
        $department = Department::where('name', $request->department)->first();

        $cwdepartment = Department::where('label', '财务科')->first();
        $leader = Leader::find($cwdepartment->chief_leader_id);
        $user = User::where('name', $leader->name)->first();
        $leader_2 = Leader::find($department->chief_leader_id);
        $user_2 = User::where('name', $leader_2->name)->first();

        $record = PaymentDocument::create([
            'create_date' => $request->create_date,
            'all_price' => $request->all_price,
            'status' => 'finance_audit',
            'user_id_1' => $request->user_id,
            'user_id_2' => $user_2->id,
            'user_id_3' => $user->id,
        ]);
        $record->save();
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
                if (!is_null($contract->equipment_apply_record_id)) {
                    $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                }

                $text = $payment_process->assessments_count == 0 ? '首款' : '尾款';
                $payment_process->notification()->delete();
                array_push($data, [
                    'company' => $contract->contractor,
                    'equipment' => $equipment_apply_record->equipment,
                    'price' => $contract->price,
                    'type' => $text,
                    'last_pay_date' => is_null($last_payment_process_record) ? '' : $last_payment_process_record->payment_date,
                    'assessments_count' => $payment_process->assessments_count,
                    'assessment' => $payment_process_record->assessment,
                    'rest_money' => $contract->price - $payment_process->assessments_count - $payment_process_record->assessment,
                    'payment_terms_now' => $payment_process_record->payment_terms,
                    'payment_terms' => $contract->payment_terms,
                ]);
            }
        }
        $today = date('Y年m月d日');
        $notification = Notification::create([
            'user_id' => $record->user_id_3,
            'title' => $today . "制单",
            'body' => json_encode($record),
            'category' => 'purchaseMonitor',
            'n_category' => 'paymentDocument',
            'type' => 'finance_audit',
            'link' => '/purchase/paymentDocument/detail#finance_audit&' . $record->id,
        ]);
        $record->notification()->save($notification);
        return response()->json([
            'data' => $data,
            // 'signature' => $signatures,
            'id' =>  $record->id,
        ]);
    }

    public function storeXlsx(Request $request, PaymentDocument $record){
        $attributes = $request->only(['excel_url']);
        $record->update($attributes);
        return new PaymentDocumentResource($record);
    }

    public function update(Request $request, PaymentDocument $record) {
        $payment_process_records = PaymentProcessRecord::where('payment_document_id', $record->id)->get();
        switch ($record->status) {
            case 'finance_audit': 
                $payment_process_record = PaymentProcessRecord::where('payment_document_id', $record->id)->first();
                $department = Department::where('label', $payment_process_record->department)->first();
                $leader = Leader::find($department->leader_id);
                $user = User::where('name', $leader->name)->first();
                $record->update([
                    'status'=> 'dean_audit',
                    'user_id_4' => $user->id,
                ]);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'dean_audit']);
                }
                $old_notification = $record->notification()->first();
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'title' => $old_notification->title,
                    'body' => json_encode($record),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentDocument',
                    'type' => 'dean_audit',
                    'link' => '/purchase/paymentDocument/detail#dean_audit&' . $record->id,
                ]);
                $record->notification()->delete();
                $record->notification()->save($notification);
                return response()->json([])->setStatusCode(200); 
            case 'dean_audit':
                $department = Department::where('label', '财务科')->first();
                $leader = Leader::find($department->leader_id);
                $user = User::where('name', $leader->name)->first();
                if($user->id !== $record->user_id_4) {
                    $record->update([
                        'status' => 'finance_dean_audit',
                        'user_id_5' => $user->id,
                    ]);
                    foreach ($payment_process_records as $payment_process_record) {
                        $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                        $payment_process->update(['status' => 'finance_dean_audit']);
                    }
                    $old_notification = $record->notification()->first();
                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'title' => $old_notification->title,
                        'body' => json_encode($record),
                        'category' => 'purchaseMonitor',
                        'n_category' => 'paymentDocument',
                        'type' => 'finance_dean_audit',
                        'link' => '/purchase/paymentDocument/detail#finance_dean_audit&' . $record->id,
                    ]);
                    $record->notification()->delete();
                    $record->notification()->save($notification);
                    return response()->json([])->setStatusCode(200);         
                } else {
                    $record->update([
                        'status' => 'upload',
                        'user_id_5' => $user->id,
                    ]);
                    foreach ($payment_process_records as $payment_process_record) {
                        $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                        $payment_process->update(['status' => 'upload']);
                    }
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
                }
             case 'finance_dean_audit':
                $record->update(['status' => 'upload']);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'upload']);
                }
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
                    $equipment_apply_recordJSON = json_encode($equipment_apply_record, true);
                    $equipment_apply_record_array = json_decode($equipment_apply_recordJSON, true);
                    $processJSON = json_encode($payment_process, true);
                    $process_array = json_decode($processJSON, true);
                    $information = (object) array_merge($record_array, $equipment_apply_record_array, $process_array);
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
            ]);
        }
        $record->delete();
        return response()->json([])->setStatusCode(200);
    }
}
