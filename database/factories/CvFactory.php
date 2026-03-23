<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $name,
            'email' => $email,
            'phone' => fake('en_GB')->phoneNumber(),
            'address' => fake('en_GB')->address(),
            'location' => fake('en_GB')->city(),
            'links' => fake('en_GB')->url(),
            'bio' => fake('en_GB')->paragraph(),
            'experience' => fake('en_GB')->sentence(),
            'education' => fake('en_GB')->sentence(),
            'skills' => fake('en_GB')->sentence(),
        ];
    }
}
