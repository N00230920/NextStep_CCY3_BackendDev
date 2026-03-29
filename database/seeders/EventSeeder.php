<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::factory()
            ->count(25)
            ->recycle(User::all())
            ->recycle(Application::all())
            ->create();
    }
}
