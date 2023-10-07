<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentPlan;
use App\Models\Department;
use App\Http\Resources\PaymentPlanResource;

class PaymentPlansController extends Controller
{
    public function index(Request $request, PaymentPlan $plan){
        $query = $plan->query();
        $plans = $query->paginate();
        
        return  PaymentPlanResource::collection($plans);
    }

    public function getItem(Request $request, PaymentPlan $plan){
        $plan = PaymentPlan::find($request->id);
        return new PaymentPlanResource($plan);
    }

    public function store(Request $request){
        $department = Department::where('name', $request->department)->first();
        $plan = PaymentPlan::create([
            'contract_name' => $request->contract_name,
            'department' => $department->label,
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

        return new PaymentPlanResource($plan);
    }
    
    public function delete(Request $request, PaymentPlan $plan){
        $plan->delete();
        return response()->json([])->setStatusCode(201);
    }
}
