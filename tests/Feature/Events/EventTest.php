<?php

namespace Tests\Feature\Events;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Events\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function testAuthorizedEventSchedule()
    {

        $admin = self::createUserWithRole('admin');
        $token = self::authToken($admin);

        $payload = [
            'name'                 => 'Tech Conference',
            'start_time'           => '2025-05-01 09:00:00',
            'end_time'             => '2025-05-01 17:00:00',
            'max_participants'     => 80,
            'current_participants' => 0,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(self::eventUrl . '/schedule', $payload)
            ->assertCreated();

        $this->assertDatabaseHas('events', [
            'name'             => 'Tech Conference',
            'start_time'       => '2025-05-01 09:00:00',
            'end_time'         => '2025-05-01 17:00:00',
            'max_participants' => 80,
        ]);

        $response->assertJsonFragment([
            'name' => 'Tech Conference',
            'max_participants' => 80,
        ]);

        $response->assertJsonPath('status', true);
        $response->assertJsonPath('message', 'Resource created!');
    }


    public function testUnauthorizedEventSchedule()
    {
        $user = self::createUserWithRole('user');
        $token = self::authToken($user);

        $payload = [
            'name'             => 'Tech Conference',
            'start_time'       => '2025-05-01 09:00:00',
            'end_time'         => '2025-05-01 17:00:00',
            'max_participants'    => 20,
            'current_participants' => 0
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(self::eventUrl . '/schedule', $payload);


        $response->assertForbidden()
            ->assertJson([
                'message' => 'Only admin, manager authorized action!',
            ]);

        $this->assertDatabaseMissing('events', [
            'name' => 'Tech Conference',
        ]);
    }


    public function testFetchSingleEvent()
    {

        $event = Event::factory()->create([
            'name'       => 'Gymnasium 2029',
            'start_time' => '2025-05-01 09:00:00',
            'end_time'   => '2025-05-01 17:00:00',
        ]);


        $user =  self::createUserWithRole('user');
        $token = self::authToken($user);

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(self::eventUrl . "/{$event->id}")
            ->assertSuccessful()
            ->assertJson([
                'status' => true,
                'message' => 'Request completed!',
                'data' => [
                    'id'         => (string) $event->id,
                    'name'       => 'Gymnasium 2029',
                    'start_time' => $event->start_time->toIso8601String(),
                    'end_time'   => $event->end_time->toIso8601String(),
                    "max_participants"     => $event->max_participants,
                    "current_participants" => 0,
                ],
            ]);
    }


    public function testFetchEvents()
    {
        Event::factory()->count(4)->create();
        $adminUser =  self::createUserWithRole('admin');
        $token = self::authToken($adminUser);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(self::eventUrl);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Request completed!',
            ])
            ->assertJsonStructure([
                'data' => [
                    'events' => [
                        '*' => [
                            'id',
                            'name',
                            'start_time',
                            'end_time',
                            'max_participants',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'total',
                    ],
                ],
            ]);

        $this->assertEquals(4, $response->json('data.meta.total'));
    }


    public function testToConfirmOverlappingEventRegistration()
    {
        $user = self::createUserWithRole('user');
        $token = self::authToken($user);

        $initialEvent = Event::factory()->create([
            'start_time' => '2025-05-01 09:00:00',
            'end_time'   => '2025-05-01 12:00:00',
        ]);
        $finalEvent = Event::factory()->create([
            'start_time' => '2025-05-01 11:00:00',
            'end_time'   => '2025-05-01 14:00:00',
        ]);

        DB::table('registrations')->insert(
            [
                'id'         => Str::uuid(),
                'event_id'   => $initialEvent->id,
                'user_id'    => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(
                self::eventUrl . "/participant/register",
                [
                    'event_id' => $finalEvent->id
                ]
            );

        $response->assertStatus(422)
            ->assertJson(
                [
                    'status' => false,
                    'message' => 'Validation errors',
                    'errors' => [
                        'event_id' => [
                            'You are already registered for an overlapping event.'
                        ]
                    ]
                ]
            );
    }
}
