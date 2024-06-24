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
use App\Models\User;
use App\Models\Leader;


class PaymentDocumentController extends Controller
{

    public function index(Request $request, PaymentDocument $record){
        $query = $record->query();

        $records = $query->paginate();
    
        return  PaymentDocumentResource::collection($records);
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
        return response()->json([
            'data' => $data,
            'id' =>  $record->id,
        ]);
    }

    public function store(Request $request){
        $department = Department::where('name', $request->department)->first();

        $record = PaymentDocument::create([
            'create_date' => $request->create_date,
            'all_price' => $request->all_price,
            'status' => 'finance_audit',
            'user_id_1' => $request->user_id,
            'user_id_2' => $department->chief_leader_id,
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
                if ($contract->equipment_apply_record_id) {
                    $equipment_apply_record = EquipmentApplyRecord::find($contract->equipment_apply_record_id);
                }

                $text = $payment_process->assessments_count == 0 ? '首款' : '尾款';
                
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
            $user_1_signature = User::find($record->user_id_1)->signature_picture;
            $leader_2 = Leader::find($record->user_id_2);
            $user_2_signature = User::where('name', $leader_2->name)->first()->signature_picture;
            $signatures = [$user_1_signature, $user_2_signature];
        }
        return response()->json([
            'data' => $data,
            'signature' => $signatures,
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
                $record->update(['status'=> 'dean_audit']);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'dean_audit']);
                }
                $department = Department::where('label', '财务科')->first();
                $leader = Leader::find($department->chief_leader_id);
                $user = User::where('name', $leader->name)->first();
                return response()->json([
                    'excel_url' => $record->excel_url,
                    'user_id' => $user->id,
                    'signature' => $user->signature_picture,
                 ]);
            case 'dean_audit':
                $record->update(['status' => 'finance_dean_audit']);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'finance_dean_audit']);
                }
                $department = Department::where('label', '财务科')->first();
                $leader = Leader::find($department->chief_leader_id);
                $user = User::where('name', $leader->name)->first();
                return response()->json([
                    'excel_url' => $record->excel_url,
                    'user_id' => $user->id,
                    'signature' => $user->signature_picture,
                 ]);
             case 'finance_dean_audit':
                $record->update(['status' => 'finish']);
                foreach ($payment_process_records as $payment_process_record) {
                    $payment_process = PaymentProcess::find($payment_process_record->payment_process_id);
                    $payment_process->update(['status' => 'process']);
                }
                break;
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
