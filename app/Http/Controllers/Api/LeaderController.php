<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderResource;
use App\Models\Department;
use App\Models\Leader;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function index(Request $request, Leader $leader)
    {
        $query = $leader->query();

        if (!is_null($request->isPaginate)) {
            $leaders = $query->paginate();
        } else {
            $leaders = $query->get();
        }
        
        return LeaderResource::collection($leaders);
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_id);
        $leader = Leader::create([
            'name' =>  $user->name,
            'type' => $request->type,
        ]);
        $leader->user()->save($user);
    }

    public function update(Request $request, Leader $leader) {
        $departments = $leader->departments()->get();
        if ($leader->type == 'leader') {
            $departments->each(function ($department) {
                $department->update([
                    'leader_id' => null,
                ]);
            });            
        } else if ($leader->type == 'chief_leader') {
            $departments->each(function ($department) {
                $department->update([
                    'chief_leader_id' => null,
                ]);
            });   
        }
            
        $department_ids = explode('&', $request->department_id);
        foreach ($department_ids as $department_id) {
            $department = Department::where('name', $department_id)->where('is_functional', '1')->first();
            if ($department) {
                if ($leader->type == 'leader') {
                    $department->update([
                        'leader_id' => $leader->id,
                    ]);
                } else if ($leader->type == 'chief_leader') {
                    $department->update([
                        'chief_leader_id' => $leader->id,
                    ]);
                    return $department;
                } 
            }
        }
        return new LeaderResource($leader);
    }

    public function delete(Request $request, Leader $leader) {
        $leader->user()->update([
            'leader_id' => null,
        ]);
        $leader->delete();
        return response()->json([])->setStatusCode(200);
    }
}
