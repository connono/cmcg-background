<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Models\PaymentPlan;
use App\Models\PaymentProcess;
use App\Models\Notification;
use App\Http\Resources\PaymentRecordResource;

class PaymentRecordsController extends Controller
{
    public function planIndex(Request $request, PaymentPlan $plan){
        $records = $plan->records->diff(PaymentRecord::whereNull('assessment_date')->where('type', 'plan')->get());
        
        return  PaymentRecordResource::collection($records);
    }

    public function processIndex(Request $request, PaymentProcess $process){
        $records = $process->records->diff(PaymentRecord::whereNull('assessment_date')->where('type', 'process')->get());
        
        return  PaymentRecordResource::collection($records);
    }

    public function getItem(Request $request, PaymentRecord $record){
        $record = PaymentRecord::find($request->id);
        return new PaymentRecordResource($record);
    }

    public function store(Request $request){
        $record = PaymentRecord::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'next_date' => $request->next_date,
            'type' => $request->type,
        ]);
        $record->save();

        if ($request->type == 'plan') {
            $plan = PaymentPlan::find($request->plan_id);
            
            $plan->records()->save($record);
            $plan->update([
                'next_date' => $request->next_date,
                'current_payment_record_id' => $record->id,
            ]);
            if($plan->notification()) $plan->notification()->delete();    
        } else if($request->type == 'process') {
            $process = PaymentProcess::find($request->plan_id);
            $process->records()->save($record);
            $process->update([
                'next_date' => $request->next_date,
                'current_payment_record_id' => $record->id,
            ]);
            if($process->notification()) $process->notification()->delete();  
        }
        return new PaymentRecordResource($record);
    }

    public function update(Request $request, PaymentRecord $record){
        $item = null;
        switch($request->type) {
            case 'plan': 
                $item = PaymentPlan::find($request->plan_id);
                break;
            case 'process':
                $item = PaymentProcess::find($request->process_id);
        }
        switch($request->method) {
            case 'apply':
                $attributes = $request->only(['assessment', 'payment_voucher_file']);
                $item->update([
                    'status' => 'audit',
                    'assessment' => $request->assessment,
                ]);
                $notification = Notification::create([
                    'permission' => 'can_audit_payment_record',
                    'title' => $item->contract_name,
                    'body' => json_encode($item),
                    'link' => '/paymentProcess/detail#audit&' . $item->id . '&' . $item->current_payment_record_id,
                ]);
                $item->notification()->delete();
                $item->notification()->save($notification);
                break;
            case 'audit':
                $attributes = [];
                $item->update(['status' => 'process']);
                $notification = Notification::create([
                    'permission' => 'can_process_payment_record',
                    'title' => $item->contract_name,
                    'body' => json_encode($item),
                    'link' => '/paymentProcess/detail#process&' . $item->id . '&' . $item->current_payment_record_id,
                ]);
                $item->notification()->delete();
                $item->notification()->save($notification);
                break;
            case 'process':
                $attributes = $request->only(['assessment_date', 'payment_file']);
                $assessments_count = $item->assessments_count + $record->assessment;
                $records_count = $item->records_count + 1;
                if ($item->target_amount && $item->target_amount == $assessments_count){
                    $item->update([
                        'status' => 'stop',
                        'assessment' => null,
                        'next_date' => null,
                        'records_count' => $records_count,
                        'assessments_count' => $assessments_count,
                    ]);
                    break;
                }
                $item->update([
                    'status' => 'wait',
                    'assessment' => null,
                    'next_date' => null,
                    'records_count' => $records_count,
                    'assessments_count' => $assessments_count, 
                ]);
                $notification = Notification::create([
                    'permission' => $item->department,
                    'title' => $item->contract_name,
                    'body' => json_encode($item),
                    'link' => '/paymentProcess',
                ]);
                $item->notification()->delete();
                $item->notification()->save($notification);
                break;
            
        }
        $record->update($attributes);
        return new PaymentRecordResource($record);
    }

    public function back(Request $request, PaymentRecord $record) {
        $item = null;
        switch($request->type) {
            case 'plan': 
                $item = PaymentPlan::find($request->plan_id);
                break;
            case 'process':
                $item = PaymentProcess::find($request->process_id);
        }
        $item->update(['status' => 'apply']);
        $record->update([
            'assessment' => null,
            'payment_voucher_file' => null,
        ]);
        $notification = Notification::create([
            'permission' => $item->department,
            'title' => $item->contract_name,
            'body' => json_encode($item),
            'link' => '/paymentProcess/detail#apply&' . $item->id . '&' . $item->current_payment_record_id,
        ]);
        $item->notification()->delete();
        $item->notification()->save($notification);
        return new PaymentRecordResource($record);
    }

    
    public function delete(Request $request, PaymentRecord $record){
        $plan = PaymentPlan::find($request->plan_id);
        $records_count = $plan->records_count - 1;
        $assessments_count = $plan->assessments_count - $record->assessment;
        $plan->update([
            'next_date' => null,
            'assessment' => null,
            'records_count' => $records_count,
            'assessments_count' => $assessments_count, 
        ]);
        $record->delete();
        return response()->json([])->setStatusCode(201);
    }
}
