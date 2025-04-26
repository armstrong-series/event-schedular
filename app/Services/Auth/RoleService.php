<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{


    public function assignRole(User $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $user->role_id = $role->id;
        return $user->save();
    }


    public function getRoleByName(string $roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            throw new ModelNotFoundException("Role '{$roleName}' not found.");
        }

        return $role;
    }

    public function userHasRole(User $user, string $roleName): bool
    {
        return $user->role && $user->role->name === $roleName;
    }

    public function roles()
    {
        return Role::select('name')->get();
    }

    public function getRoleById(string $roleId): Role
    {
        return Role::findOrFail($roleId);
    }
}
