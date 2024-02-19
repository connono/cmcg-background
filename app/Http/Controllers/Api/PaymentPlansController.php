<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Models\PaymentPlan;
use App\Models\Department;
use App\Http\Resources\PaymentPlanResource;

class PaymentPlansController extends Controller
{
    public function index(Request $request, PaymentPlan $plan){
        $query = $plan->query();
        if ($request->department && $request->department !== '财务科') {
            $query = $query->where('department', $request->department);
        }
        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
        if ($request->contract_name) {
            $query = $query->where('contract_name', 'like', '%'.$request->contract_name.'%');
        }
        $plans = $query->get();
        
        return  PaymentPlanResource::collection($plans);
    }

    public function getItem(Request $request, PaymentPlan $plan){
        $plan = PaymentPlan::find($request->id);
        return new PaymentPlanResource($plan);
    }

    public function store(Request $request){
        $plan = PaymentPlan::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'is_pay' => $request->is_pay,
            'category' => $request->category,
            'finish_date' => $request->finish_date,
            'contract_date' => $request->contract_date,
            'payment_file' => $request->payment_file,
        ]);
        $plan->records_count = 0;
        $plan->assessments_count = 0;
        $plan->save();

        if ($request->contract_id) {
            $contract = Contract::find($request->contract_id);
            $contract_name = $contract->contract_name . $contract->series_number . '-' . $plan->id;
            $plan->update([
                'contract_name' => $contract_name,
            ]);
            $contract->plans()->save($plan);
        }

        return new PaymentPlanResource($plan);
    }

    public function stop(Request $request, PaymentPlan $plan) {
        $plan->update([
            'status' => 'stop'
        ]);
        $plan->notification()->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function delete(Request $request, PaymentPlan $plan) {
        foreach($plan->records as $record){
            $record->delete();
        }
        $plan->delete();
        return response()->json([])->setStatusCode(201);
    }
}
