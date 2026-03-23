<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cv;
use Illuminate\Database\Seeder;

class CvSeeder extends Seeder
{
    public function run(): void
    {
        Cv::factory()->count(10)->create();
    }
}