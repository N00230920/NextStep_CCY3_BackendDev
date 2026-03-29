<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => User::factory(),
        'application_id' => Application::factory(),
        'title' => fake('en_GB')->sentence(),
        'event_type' => fake('en_GB')->randomElement(['interview', 'reminder', 'assessment', 'call','deadline']),
        'description' => fake('en_GB')->paragraph(),
        'event_date' => fake('en_GB')->date(),
        'is_all_day' => fake('en_GB')->boolean(),
        'event_time' => fake('en_GB')->time(),
        'location' => fake('en_GB')->city(),
        ];
    }
}
