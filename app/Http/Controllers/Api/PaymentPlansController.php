<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentPlan;
use App\Http\Resources\PaymentPlanResource;
use App\Handlers\ImageUploadHandler;

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

    public function store(Request $request, ImageUploadHandler $uploader){
        $plan = PaymentPlan::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'is_pay' => $request->is_pay,
            'category' => $request->category,
            'finish_date' => $request->finish_date,
            'contract_date' => $request->contract_date,
        ]);
        if ($request->payment_file) {
            $result = $uploader->save($request->payment_file, 'pay', $request->department);
            if ($result) {
                $plan->payment_file = $result['path'];
            }
        }
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
