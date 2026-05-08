<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Cover;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 25 covers, recycling existing users and applications to associate with the covers
        Cover::factory()
            ->count(25)
            ->recycle(User::all())
            ->recycle(Application::all())
            ->create();
    }
}
