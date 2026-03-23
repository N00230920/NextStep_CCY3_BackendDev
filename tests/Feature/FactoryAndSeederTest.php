<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FactoryAndSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_factory(): void
    {
        $user = User::factory()->make();

        $this->assertInstanceOf(User::class,$user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);

        $user = User::factory()->make();
        $this->assertDataMissing('users', ['email' => $user->email]);
        $user = User::factory()->create();
        $this->assertDataMissing('users', ['email' => $user->email]);
    }

    public function test_user_seeder(): void
    {
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 10);
    }

    public function test_cv_factory(): void
    {
        $cv = Cv::factory()->make();

        $this->assertInstanceOf(Cv::class, $cv);

        $this->assertNotNull($cv->name);
        $this->assertNotNull($cv->email);
        $this->assertNotNull($cv->phone);
        $this->assertNotNull($cv->address);
        $this->assertNotNull($cv->location);
        $this->assertNotNull($cv->links);
        $this->assertNotNull($cv->bio);
        $this->assertNotNull($cv->experience);
        $this->assertNotNull($cv->education);
        $this->assertNotNull($cv->skills);

        $user = Cv::factory()->make();
        $this->assertDataMissing('cvs', ['name' => $cv->name]);
        $user = Cv::factory()->create();
        $this->assertDataMissing('cvs', ['name' => $cv->name]);
    }

    public function test_cv_seeder(): void
    {
        $this->seed(CvSeeder::class);
        $this->assertDatabaseCount('cvs', 10);
    }
}
