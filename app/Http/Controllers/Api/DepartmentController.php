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
        $departments = $query->paginate();
        return $departments;
    }
}
