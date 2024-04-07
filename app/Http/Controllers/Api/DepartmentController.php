<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;


class DepartmentController extends Controller
{
    public function index(Request $request, Department $department){
        $query = $department->query();
        if (!is_null($request->is_functional)) {
            $query = $query->where('is_functional', '' . $request->is_functional);
        }
        if (!is_null($request->is_paginate) && $request->is_paginate == 'true') {
            $departments = $query->paginate();
            return DepartmentResource::collection($departments);
        } else {
            $departments = $query->get();
            return response()->json([
                'data' => $departments,
            ])->setStatusCode(200);    
        }
    }

    public function engineerIndex(Request $request, Department $department){
        $query = $department->query();
        $query = $query->whereNull('engineer_id');
        if (!is_null($request->is_functional)) {
            $query = $query->where('is_functional', '' . $request->is_functional);
        }
        $departments = $query->get();
        return response()->json([
            'data' => $departments,
        ])->setStatusCode(200);
    }

    public function leaderIndex(Request $request, Department $department){
        $query = $department->query();
        $query = $query->whereNull('leader_id');
        if (!is_null($request->is_functional)) {
            $query = $query->where('is_functional', '' . $request->is_functional);
        }
        $departments = $query->get();
        return response()->json([
            'data' => $departments,
        ])->setStatusCode(200);
    }

    public function updateLeader(Request $request, Department $department){
        $department->update([
            'leader_id' => $request->leader_id,
        ]);
        return new DepartmentResource($department);
    }
}
