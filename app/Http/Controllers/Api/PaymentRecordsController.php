<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Models\PaymentPlan;
use App\Models\Notification;
use App\Http\Resources\PaymentRecordResource;

class PaymentRecordsController extends Controller
{
    public function index(Request $request, PaymentPlan $plan){
        $records = $plan->records;
        
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
        ]);
        $record->save();

        $plan = PaymentPlan::find($request->plan_id);
        
        $plan->records()->save($record);
        $plan->update([
            'next_date' => $request->next_date,
            'current_payment_record_id' => $record->id,
        ]);
        return new PaymentRecordResource($record);
    }

    public function update(Request $request, PaymentRecord $record){
        $plan = PaymentPlan::find($request->plan_id);
        switch($request->method) {
            case 'apply':
                $attributes = $request->only(['assessment', 'payment_voucher_file']);
                $plan->update([
                    'status' => 'audit',
                    'assessment' => $request->assessment,
                ]);
                $notification = Notification::create([
                    'permission' => 'can_audit_paymentplan',
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'link' => '/paymentMonitor/detail#audit&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            case 'audit':
                $attributes = [];
                $plan->update(['status' => 'process']);
                $notification = Notification::create([
                    'permission' => 'can_process_paymentplan',
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'link' => '/paymentMonitor/detail#process&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            case 'process':
                $attributes = $request->only(['assessment_date', 'payment_file']);
                $assessments_count = $plan->assessments_count + $record->assessment;
                $records_count = $plan->records_count + 1;
                $plan->update([
                    'status' => 'wait',
                    'assessment' => null,
                    'next_date' => null,
                    'records_count' => $records_count,
                    'assessments_count' => $assessments_count, 
                ]);
                $notification = Notification::create([
                    'permission' => $plan->department,
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'link' => '/paymentMonitor',
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            
        }
        $record->update($attributes);
        return new PaymentRecordResource($record);
    }

    public function back(Request $request, PaymentRecord $record) {
        $plan = PaymentPlan::find($request->plan_id);
        $plan->update(['status' => 'apply']);
        $notification = Notification::create([
            'permission' => $plan->department,
            'title' => $plan->contract_name,
            'body' => json_encode($plan),
            'link' => '/paymentMonitor/detail#apply&' . $plan->id . '&' . $plan->current_payment_record_id,
        ]);
        $plan->notification()->delete();
        $plan->notification()->save($notification);
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
