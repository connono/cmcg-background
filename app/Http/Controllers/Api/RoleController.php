<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleController extends Controller
{
    public function userAllRoles(Request $request, User $user )
    {
        $roles = $user->getRoleNames();
        return $roles;
    }

    public function createRole(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        return $role;
    }

    public function updateRole(Request $request) 
    {
        $permissions = explode('&', $request->permissions);
        $role = Role::where('name', $request->role_name)->first();
        $role->syncPermissions($permissions);
        return $role;
    }
    
    public function allRoles(Request $request)
    {
        $all_roles = Role::all();
        $all_role_permissions = $all_roles->map(function (Role $role) {
            return [
                "role" => $role->name,
                "permissions" => $role->permissions,
            ];
        });
        return $all_role_permissions;
    }
}
