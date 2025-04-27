<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class AuthTest extends TestCase
{

    use RefreshDatabase;


    public function testSuccessfulAuthentication()
    {


        $role = Role::factory()->create([
            'name' => 'manager',
        ]);
        $user = User::factory()->create(
            [
                'email'    => 'karen.winklevoss@glimpsemedia.co',
                'password' => bcrypt('password123'),
                'status'   => 'active',
                'role_id'  => $role->id
            ]
        );


        $response = $this->postJson(self::authUrl .'/signin', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role_name',
                    'status',
                    'token',
                ],
            ])
            ->assertJson([
                'status' => true,
                'message' => 'Authenticated!',
            ]);


        $this->assertNotEmpty($response->json('data.token'));
    }


    public function testFailedAuthentication()
    {

        $role = Role::factory()->create([
            'name' => 'manager',
        ]);

        $user = User::factory()->create([
            'email'    => 'eleanor.armstrong@glimpsemedia.co',
            'password' => bcrypt('correct-password'),
            'status'   => 'active',
            'role_id'  => $role->id,
        ]);
        $response = $this->postJson(self::authUrl .'/signin', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid credentials!',
                'data'    => [],
            ]);
    }
}
