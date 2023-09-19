<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionsController extends Controller
{
    public function store(Request $request)
    {
        $user = User::find($request->id);
        $user->assignRole($request->role);
        return $user;
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->removeRole($request->old_role);
        $user->assignRole($request->new_role);
        return $user;
    }

    public function index(Request $request, User $user)
    {
        
        $permissions = $user->getAllPermissions();

        return $permissions;
    }
    
    public function allRoles(Request $request)
    {
        
        $all_roles = Role::all()->pluck('name');
        return $all_roles;
    }
}
