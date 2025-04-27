<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\Auth\RoleService;
use App\Models\Role;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public const eventUrl = '/events';
    public const authUrl = '/auth';

    protected RoleService $roleService;
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        $this->artisan('migrate');

        $this->roleService = app(RoleService::class);

        Role::factory()->create(['name' => 'admin']);
        Role::factory()->create(['name' => 'user']);
    }


    protected function createUserWithRole(string $roleName): User
    {
        $role = Role::where('name', $roleName)->first();

        $user = User::factory()->create(['role_id' => $role->id]);

        $this->roleService->assignRole($user, $roleName);

        return $user;
    }


    protected function authToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }
}
