<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentProcess;
use App\Models\Department;
use App\Http\Resources\PaymentProcessResource;

class PaymentProcessesController extends Controller
{
    public function index(Request $request, PaymentProcess $process){
        $query = $process->query();
        if ($request->department && $request->department !== '财务科') {
            $query = $query->where('department', $request->department);
        }
        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
        if ($request->contract_name) {
            $query = $query->where('contract_name', 'like', '%'.$request->contract_name.'%');
        }
        $processes = $query->get();
        return  PaymentProcessResource::collection($processes);
    }

    public function getItem(Request $request, PaymentProcess $process){
        $process = PaymentProcess::find($request->id);
        return new PaymentProcessResource($process);
    }

    public function store(Request $request){
        $process = PaymentProcess::create([
            'contract_name' => $request->contract_name,
            'department' => $request->department,
            'company' => $request->company,
            'is_pay' => $request->is_pay,
            'category' => $request->category,
            'contract_date' => $request->contract_date,
            'payment_file' => $request->payment_file,
        ]);
        $process->target_amount = $request->target_amount;
        $process->records_count = 0;
        $process->assessments_count = 0;
        $process->save();

        return new PaymentProcessResource($process);
    }

    public function stop(Request $request, PaymentProcess $process) {
        $process->update([
            'status' => 'stop'
        ]);
        $process->notification()->delete();
        return response()->json([])->setStatusCode(201);
    }
    
    public function delete(Request $request, PaymentProcess $process) {
        foreach($process->records as $record){
            $record->delete();
        }
        $process->delete();
        return response()->json([])->setStatusCode(201);
    }
}
