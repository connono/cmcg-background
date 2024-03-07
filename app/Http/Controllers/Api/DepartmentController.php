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
        $departments = $query->get();
        return response()->json([
            'data' => $departments,
        ])->setStatusCode(201);
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
        ])->setStatusCode(201);
    }
}
