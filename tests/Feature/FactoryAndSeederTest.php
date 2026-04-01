<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Cover;
use App\Models\Cv;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\ApplicationSeeder;
use Database\Seeders\CoverSeeder;
use Database\Seeders\CvSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryAndSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory(): void
    {
        $user = User::factory()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);

        $user = User::factory()->make();
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_user_seeder(): void
    {
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 25);
    }

    public function test_cv_factory(): void
    {
        $cv = Cv::factory()->make();

        $this->assertInstanceOf(Cv::class, $cv);

        $this->assertNotNull($cv->name);
        $this->assertNotNull($cv->email);
        $this->assertNotNull($cv->phone);
        $this->assertNotNull($cv->location);
        $this->assertNotNull($cv->links);
        $this->assertNotNull($cv->bio);
        $this->assertNotNull($cv->experience);
        $this->assertNotNull($cv->education);
        $this->assertNotNull($cv->skills);

        $cv = Cv::factory()->make();
        $this->assertDatabaseMissing('cvs', ['name' => $cv->name]);
        $cv = Cv::factory()->create();
        $this->assertDatabaseHas('cvs', ['name' => $cv->name]);
    }

    public function test_cv_seeder(): void
    {
        $this->seed(CvSeeder::class);
        $this->assertDatabaseCount('cvs', 25);
    }

    public function test_application_factory(): void
    {
        $application = Application::factory()->make();

        $this->assertInstanceOf(Application::class, $application);

        $this->assertNotNull($application->company_name);
        $this->assertNotNull($application->position);
        $this->assertNotNull($application->location);
        $this->assertNotNull($application->contact_email);
        $this->assertNotNull($application->salary);
        $this->assertNotNull($application->status);
        $this->assertNotNull($application->job_type);
        $this->assertNotNull($application->job_url);
        $this->assertNotNull($application->notes);
        $this->assertNotNull($application->applied_date);

        $application = Application::factory()->make();
        $this->assertDatabaseMissing('applications', ['company_name' => $application->company_name]);
        $application = Application::factory()->create();
        $this->assertDatabaseHas('applications', ['company_name' => $application->company_name]);
    }

    public function test_application_seeder(): void
    {
        $this->seed(ApplicationSeeder::class);
        $this->assertDatabaseCount('applications', 25);
    }

    public function test_cover_factory(): void
    {
        $table = (new Cover())->getTable();
        $cover = Cover::factory()->make();

        $this->assertInstanceOf(Cover::class, $cover);

        $this->assertNotNull($cover->title);
        $this->assertNotNull($cover->content);

        $cover = Cover::factory()->make();
        $this->assertDatabaseMissing($table, ['title' => $cover->title]);
        $cover = Cover::factory()->create();
        $this->assertDatabaseHas($table, ['title' => $cover->title]);
    }

    public function test_cover_seeder(): void
    {
        $table = (new Cover())->getTable();
        $this->seed(CoverSeeder::class);
        $this->assertDatabaseCount($table, 25);
    }

    public function test_event_factory(): void
    {
        $table = (new Event())->getTable();
        $event = Event::factory()->make();

        $this->assertInstanceOf(Event::class, $event);

        $this->assertNotNull($event->title);
        $this->assertNotNull($event->event_type);
        $this->assertNotNull($event->description);
        $this->assertNotNull($event->event_date);
        $this->assertNotNull($event->is_all_day);
        $this->assertNotNull($event->event_time);
        $this->assertNotNull($event->location);

        $event = Event::factory()->make();
        $this->assertDatabaseMissing($table, ['title' => $event->title]);
        $event = Event::factory()->create();
        $this->assertDatabaseHas($table, ['title' => $event->title]);
    }

    public function test_event_seeder(): void
    {
        $table = (new Event())->getTable();
        $this->seed(EventSeeder::class);
        $this->assertDatabaseCount($table, 25);
    }
}
