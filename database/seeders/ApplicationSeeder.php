<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Create a few sample users if they don't exist
        $users = User::count() < 3
                    ? collect([
                        User::create([
                            'name' => 'Alexa Developer',
                            'email' => 'alexa@example.com',
                            'password' => bcrypt('password'),
                        ]),
                        User::create([
                            'name' => 'Mark Kiplier',
                            'email' => 'mark@example.com',
                            'password' => bcrypt('password'),
                        ]),
                        User::create([
                            'name' => 'Connor Coder',
                            'email' => 'connor@example.com',
                            'password' => bcrypt('password'),
                        ]),
                    ])
                    : User::take(3)->get();

        // Sample applications
        $applications = [
            'Just discovered Laravel - where has this been all my life?',
            'Building something cool with Laravel today!',
            'Laravel\'s Eloquent ORM is pure magic ',
            'Deployed my first app with Laravel Cloud. So smooth!',
            'Who else is loving Blade components?',
            'Friday deploys with Laravel? No problem!',
        ];

        // Create applications for random users
        foreach ($applications as $message) {
            $users->random()->applications()->create([
                'message' => $message,
                'created_at' => now()->subMinutes(rand(5, 1440)),
            ]);
        }
    }
}