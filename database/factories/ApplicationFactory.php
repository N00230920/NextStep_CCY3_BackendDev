<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Cv;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
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
        'cv_id' => fake('en_GB')->boolean(70) ? Cv::factory() : null,
        'company_name' => fake('en_GB')->company(),
        'position' => fake('en_GB')->jobTitle(),
        'location' => fake('en_GB')->city(),
        'contact_email' => fake('en_GB')->email(),
        'salary' => fake('en_GB')->randomNumber(5),
        'status' => fake('en_GB')->randomElement(['applied', 'interview', 'offer', 'rejected', 'ghosted']),
        'job_type' => fake('en_GB')->randomElement(['full-time', 'part-time', 'internship', 'contract']),
        'job_url' => fake('en_GB')->url(),
        'notes' => fake('en_GB')->paragraph(),
        'applied_date' => fake('en_GB')->date(),
        ];
    }
}
