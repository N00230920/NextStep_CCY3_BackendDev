<?php

namespace Database\Factories;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cv>
 */
class CvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake('en_GB')->company();
        $email = fake('en_GB')->username() . '@' . Str::slug($name) . '.com';
        $phone = fake('en_GB')->phoneNumber();
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'location' => fake('en_GB')->city(),
            'links' => fake('en_GB')->url(),
            'bio' => fake('en_GB')->paragraph(),
            'experience' => fake('en_GB')->sentence(),
            'education' => fake('en_GB')->sentence(),
            'skills' => fake('en_GB')->sentence(),
        ];
    }
}
