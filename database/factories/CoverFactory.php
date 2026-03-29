<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Cover;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cover>
 */
class CoverFactory extends Factory
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
            "title" => fake('en_GB')->sentence(),
            "content" => fake('en_GB')->paragraph()
        ];
    }
}
