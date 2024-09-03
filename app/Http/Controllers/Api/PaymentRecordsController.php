<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leader;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Models\PaymentPlan;
use App\Models\Notification;
use App\Http\Resources\PaymentRecordResource;

class PaymentRecordsController extends Controller
{
    public function index(Request $request, PaymentPlan $plan){
        $records = $plan->records->diff(PaymentRecord::whereNull('assessment_date')->where('type', 'plan')->get());
        
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
        $plan = PaymentPlan::find($request->plan_id);
        
        $plan->records()->save($record);
        $plan->update([
            'next_date' => $request->next_date,
            'current_payment_record_id' => $record->id,
        ]);
        if($plan->notification()) $plan->notification()->delete();    
        return new PaymentRecordResource($record);
    }

    public function update(Request $request, PaymentRecord $record){
        $plan = PaymentPlan::find($request->plan_id);
        switch($request->method) {
            case 'apply':
                $attributes = $request->only(['assessment', 'payment_voucher_file']);
                $plan->update([
                    'status' => 'dean_audit',
                    'assessment' => $request->assessment,
                ]);
                $record->update($attributes);
                $department1 = Department::where('label', $plan->department)->first();
                $leader = Leader::find($department1->leader_id);
                $user = User::where('name', $leader->name)->first();
                $notification = Notification::create([
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'user_id' => $user->id,
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentPlan',
                    'type' => 'dean_audit',
                    'link' => '/purchase/paymentMonitor/detail#dean_audit&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);    
                break;
            case 'dean_audit':
                $plan->update([
                    'status' => 'audit',
                ]);
                $department1 = Department::where('label', $plan->department)->first();
                $leader = Leader::find($department1->leader_id);
                $user = User::where('name', $leader->name)->first();
                $notification = Notification::create([
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'permission' => 'can_audit_payment_record',
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentPlan',
                    'type' => 'audit',
                    'link' => '/purchase/paymentMonitor/detail#audit&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            case 'audit':
                $department1 = Department::where('label', $plan->department)->first();
                $department2 = Department::where('label', '财务科')->first();
                if ($department1->leader_id === $department2->leader_id) {
                    $plan->update(['status' => 'process']);
                    $notification = Notification::create([
                        'permission' => 'can_process_payment_record',
                        'title' => $plan->contract_name,
                        'body' => json_encode($plan),
                        'category' => 'purchaseMonitor',
                        'n_category' => 'paymentPlan',
                        'type' => 'process',
                        'link' => '/purchase/paymentMonitor/detail#process&' . $plan->id . '&' . $plan->current_payment_record_id,
                    ]);
                    $plan->notification()->delete();
                    $plan->notification()->save($notification);
                } else {
                    
                    $leader = Leader::find($department2->leader_id);
                    $user = User::where('name', $leader->name)->first();
                    $plan->update(['status' => 'finance_dean_audit']);
                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'title' => $plan->contract_name,
                        'body' => json_encode($plan),
                        'category' => 'purchaseMonitor',
                        'n_category' => 'paymentPlan',
                        'type' => 'finance_dean_audit',
                        'link' => '/purchase/paymentMonitor/detail#finance_dean_audit&' . $plan->id . '&' . $plan->current_payment_record_id,
                    ]);
                    $plan->notification()->delete();
                    $plan->notification()->save($notification);    
                }
                break;
            case 'finance_dean_audit':
                $plan->update(['status' => 'process']);
                $notification = Notification::create([
                    'permission' => 'can_process_payment_record',
                    'title' => $plan->contract_name,
                    'body' => json_encode($plan),
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentPlan',
                    'type' => 'process',
                    'link' => '/purchase/paymentMonitor/detail#process&' . $plan->id . '&' . $plan->current_payment_record_id,
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            case 'process':
                $attributes = $request->only(['assessment_date', 'payment_file']);
                $record->update($attributes);
                $assessments_count = $plan->assessments_count + $record->assessment;
                $records_count = $plan->records_count + 1;
                if ($plan->target_amount && $plan->target_amount == $assessments_count){
                    $plan->update([
                        'status' => 'stop',
                        'assessment' => null,
                        'next_date' => null,
                        'records_count' => $records_count,
                        'assessments_count' => $assessments_count,
                    ]);
                    break;
                }
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
                    'category' => 'purchaseMonitor',
                    'n_category' => 'paymentPlan',
                    'type' => 'wait',
                    'link' => '/purchase/paymentMonitor',
                ]);
                $plan->notification()->delete();
                $plan->notification()->save($notification);
                break;
            
        }
        return new PaymentRecordResource($record);
    }

    public function back(Request $request, PaymentRecord $record) {
        $plan = PaymentPlan::find($request->plan_id);
        $plan->update(['status' => 'apply']);
        $record->update([
            'assessment' => null,
            'payment_voucher_file' => null,
        ]);
        $notification = Notification::create([
            'permission' => $plan->department,
            'title' => $plan->contract_name,
            'body' => json_encode($plan),
            'category' => 'purchaseMonitor',
            'n_category' => 'paymentPlan',
            'type' => 'apply',
            'link' => '/purchase/paymentMonitor/detail#apply&' . $plan->id . '&' . $plan->current_payment_record_id,
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
        return response()->json([])->setStatusCode(200);
    }
}
