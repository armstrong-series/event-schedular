<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\RoleService;
use App\Queries\AuthQuery;
use App\Contracts\AuthInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;


class AuthService implements AuthInterface
{


    public function __construct(
        public RoleService $roleService,
        protected AuthQuery $authQuery
    ) {}



    public function signin(array $credentials): array
    {

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials!');
        }

        $user = Auth::guard('api')->user();
        if ($user->status !== 'active') {
            Auth::guard('api')->logout();
            throw new AuthenticationException('Account is not active.');
        }
    
        return [
            'success' => true,
            'message' => 'Authenticated!',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role_name' => $user->role->name,
                'status'    => $user->status,
                'token'     => $token
            ],
        ];
    }

    public function me(): User
    {
        return auth()->user();
    }

    public function logout(): bool
    {
        auth()->logout();
        return true;
    }
}
