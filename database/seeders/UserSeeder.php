<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\Auth\RoleService;
use  App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $managerRole = Role::where('name', 'manager')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();
        $roleService = new RoleService();

       
        $adminUser = User::factory()->create([
            'name'   => 'Jones Calvin',
            'email'  => 'admin@glimpsemedia.co',
            'status' => 'active',
            'role_id' => $adminRole->id
        ]);
        $roleService->assignRole($adminUser, 'admin');

        $managerUser = User::factory()->create([
            'name'   => 'Eleanor Armstrong',
            'email'  => 'eleanor.armstrong@glimpsemedia.co',
            'status' => 'active',
            'role_id' => $managerRole->id,
        ]);
        $roleService->assignRole($managerUser, 'manager');

        User::factory()->count(3)->active()->create(['id' => fn() => (string) Str::uuid(),
            'role_id' => $userRole->id])->each(function ($user) use ($roleService) {
            $roleService->assignRole($user, 'user');
        });

    
        User::factory()->count(3)->locked()->create(['id' => fn() => (string) Str::uuid(),
            'role_id' => $userRole->id])->each(function ($user) use ($roleService) {
            $roleService->assignRole($user, 'user');
        });
    }
}
