<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cv;
use Illuminate\Database\Seeder;

class CvSeeder extends Seeder
{
    public function run(): void
    {
        // Create 25 CVs, recycling existing users to associate with the CVs
        Cv::factory()
            ->count(25)
            ->recycle(User::all())
            ->create();
    }
}
