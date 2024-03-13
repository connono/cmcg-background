<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EngineerResource;
use App\Models\Department;
use App\Models\Engineer;
use App\Models\User;
use Illuminate\Http\Request;

class EngineersController extends Controller
{
    public function index(Request $request, Engineer $engineer)
    {
        $query = $engineer->query();

        if (!is_null($request->isPaginate)) {
            $engineers = $query->paginate();
        } else {
            $engineers = $query->get();
        }
        
        return EngineerResource::collection($engineers);
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_id);
        $engineer = Engineer::create([
            'name' =>  $user->name,
        ]);
        $engineer->user()->save($user);
    }

    public function update(Request $request, Engineer $engineer) {
        $departments = $engineer->departments()->get();
        $departments->each(function ($department) {
            $department->update([
                'engineer_id' => null,
            ]);
        });
            
        $department_ids = explode('&', $request->department_id);
        foreach ($department_ids as $department_id) {
            $department = Department::where('name', $department_id)->where('is_functional', '0')->first();
            if ($department) {
                $engineer->departments()->save($department);
            }
        }
        return new EngineerResource($engineer);
    }

    public function delete(Request $request, Engineer $engineer) {
        $engineer->user()->update([
            'engineer_id' => null,
        ]);
        $engineer->delete();
        return response()->json([])->setStatusCode(200);
    }
}
