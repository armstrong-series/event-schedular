<?php

namespace Database\Factories\Events;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Events\Event;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Events\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Event::class;

    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 week');
        $endTime =  $this->faker->dateTimeBetween($startTime, '+2 weeks'); 

        return [
            'id' => Str::uuid(),
            'name'       => $this->faker->sentence(3),
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'max_participants' => $this->faker->numberBetween(10, 100)
        ];
    }
}
