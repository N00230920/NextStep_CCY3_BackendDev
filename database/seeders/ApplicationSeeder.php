<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Cv;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Create 25 applications, recycling existing users and CVs to associate with the applications
        Application::factory()
            ->count(25)
            ->recycle(User::all())
            ->recycle(Cv::all())
            ->create();
    }
}
