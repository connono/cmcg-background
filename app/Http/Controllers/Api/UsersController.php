<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use App\Models\Notification;

class UsersController extends Controller
{

    public function store(UserRequest $request)
    {

        $department = Department::where('name', $request->department)->first();

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => bcrypt("123456"),
            'department' => $department->label,
        ]);

        $department->users()->save($user);

        if ($request->roles) {
            $user->assignRole($request->roles);
        } else {
            $user->assignRole('用户');
        }

        return new UserResource($user);
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function update(UserRequest $request, User $user)
    {
        $department = Department::where('label', $request->department)->first();
        $user->update([
            'phone_number' => $request->phone_number,
            'department' => $department->label,
            'department_id' => $department->id,
        ]);
        $user->syncRoles($request->roles);
        return new UserResource($user);
    }

    public function delete(Request $request, User $user){
        $user->syncRoles([]);
        $user->delete();
        return response()->json([])->setStatusCode(200);
    }
    
    public function index(Request $request, User $user){
        $query = $user->query();

        if (!is_null($request->department)) {
            $query = $query->where('department', $request->department);
        }

        if (!is_null($request->isPaginate)) {
            $users = $query->paginate();
        } else {
            $users = $query->get();
        }
        
        return  UserResource::collection($users);
    }

    public function resetPassword(Request $request, User $user){
        $password = $request->password;
        $user->update([
            'password' => bcrypt($password)
        ]);
        return new UserResource($user);
    }

    public function setSignature(Request $request, User $user){
        $attributes = $request->only(['signature_picture']);
        $user->update($attributes);
        return new UserResource($user);
    }
}
