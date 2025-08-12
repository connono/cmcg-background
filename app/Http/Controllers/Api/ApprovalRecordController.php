<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ApprovalRecordResource;
use App\Models\ApprovalRecord;
use App\Models\Notification;


class ApprovalRecordController extends Controller
{

    public function getItem(Request $request, ApprovalRecord $record)
    {
        $query = $record->query();

        if (!is_null($request->approve_model)) {
            $query = $query->where('approve_model', $request->approve_model);
        }
        if (!is_null($request->approve_status)) {
            $query = $query->where('approve_status', $request->approve_status);
        }
        if (!is_null($request->approve_model_id)) {
            $query = $query->where('approve_model_id', $request->approve_model_id);
        }
        $record = $query->orderBy('id', 'DESC')->first();
        return new ApprovalRecordResource($record);
    }

    public function getList(Request $request, ApprovalRecord $record)
    {
        $query = $record->query();

        if (!is_null($request->approve_model)) {
            $query = $query->where('approve_model', $request->approve_model);
        }
        if (!is_null($request->approve_model_id)) {
            $query = $query->where('approve_model_id', $request->approve_model_id);
        }
        $records = $query->orderBy('id', 'ASC')->get();
        return ApprovalRecordResource::collection($records);
    }
    
    public function create(Request $request){

        $record = ApprovalRecord::create([
            "user_id" => $request->user_id,
            "approve_date" => $request->approve_date,
            "approve_model" => $request->approve_model,
            "approve_model_id" => $request->approve_model_id,
            "approve_status" => $request->approve_status,
            "reject_reason" => $request->reject_reason
        ]);
    

        return new ApprovalRecordResource($record);
    }

}                 
