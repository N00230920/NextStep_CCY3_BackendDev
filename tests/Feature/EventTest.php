<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Application;
use App\Models\Cv;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_event_index(): void
    {
        $this->authenticateUser();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'title',
                        'event_type',
                        'event_date',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ],
        ]);
    }

    public function test_event_show(): void
    {
        $user = $this->authenticateUser();
        $event = $user->events()->create([
            'title' => 'Test Event',
            'event_type' => 'Meeting',
            'event_date' => now()->addDays(5)->toDateString(),
            'is_all_day' => false,
            'event_time' => '14:00:00',
            'location' => 'Conference Room A',
            'description' => 'This is a test event.',
        ]);

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'application_id',
                'title',
                'event_type',
                'description',
                'event_date',
                'is_all_day',
                'event_time',
                'location',
            ],
        ]);
    }

    public function test_event_show_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/events/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);
    }

    public function test_event_store(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        Cv::factory()->create(['user_id' => $user->id]);

        $eventData = [
            'application_id' => $application->id,
            'title' => 'New Event',
            'event_type' => 'interview',
            'event_date' => now()->addDays(7)->toDateString(),
            'is_all_day' => false,
            'event_time' => '10:00',
            'location' => 'Office',
            'description' => 'Interview for the position.',
        ];

        $response = $this->postJson('/api/events', $eventData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'application_id',
                'title',
                'event_type',
                'description',
                'event_date',
                'is_all_day',
                'event_time',
                'location',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_event_store_validation_error(): void
    {
        $this->authenticateUser();

        $response = $this->postJson('/api/events', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'title',
            'event_type',
            'event_date',
            'is_all_day',
        ]);
    }

    public function test_event_update(): void
    {
        $user = $this->authenticateUser();
        $event = $user->events()->create([
            'title' => 'Test Event',
            'event_type' => 'Meeting',
            'event_date' => now()->addDays(5)->toDateString(),
            'is_all_day' => false,
            'event_time' => '14:00:00',
            'location' => 'Conference Room A',
            'description' => 'This is a test event.',
        ]);

        $updateData = [
            'title' => 'Updated Event Title',
            'event_type' => 'reminder',
            'event_date' => now()->addDays(10)->toDateString(),
            'is_all_day' => true,
            'location' => 'Updated Location',
            'description' => 'Updated description.',
        ];

        $response = $this->putJson("/api/events/{$event->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'application_id',
                'title',
                'event_type',
                'description',
                'event_date',
                'is_all_day',
                'event_time',
                'location',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_event_update_validation_error(): void
    {
        $user = $this->authenticateUser();
        $event = $user->events()->create([
            'title' => 'Test Event',
            'event_type' => 'Meeting',
            'event_date' => now()->addDays(5)->toDateString(),
            'is_all_day' => false,
            'event_time' => '14:00:00',
            'location' => 'Conference Room A',
            'description' => 'This is a test event.',
        ]);

        $response = $this->putJson("/api/events/{$event->id}", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'title',
            'event_type',
            'event_date',
            'is_all_day',
        ]);
    }

    public function test_event_update_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->putJson('/api/events/999', [
            'title' => 'Updated Event Title',
            'event_type' => 'reminder',
            'event_date' => now()->addDays(10)->toDateString(),
            'is_all_day' => true,
            'location' => 'Updated Location',
            'description' => 'Updated description.',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);
    }

    public function test_event_destroy(): void
    {
        $user = $this->authenticateUser();
        $event = $user->events()->create([
            'title' => 'Test Event',
            'event_type' => 'Meeting',
            'event_date' => now()->addDays(5)->toDateString(),
            'is_all_day' => false,
            'event_time' => '14:00:00',
            'location' => 'Conference Room A',
            'description' => 'This is a test event.',
        ]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Event deleted successfully',
        ]);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_event_destroy_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->deleteJson('/api/events/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);
    }

    public function test_event_user_cannot_access_another_users_event(): void
    {
        $owner = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id]);
        $this->authenticateUser();

        $response = $this->getJson("/api/events/{$event->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);

        $response = $this->putJson("/api/events/{$event->id}", [
            'title' => 'Updated Event Title',
            'event_type' => 'reminder',
            'event_date' => now()->addDays(10)->toDateString(),
            'is_all_day' => true,
            'location' => 'Updated Location',
            'description' => 'Updated description.',
        ]);
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);

        $response = $this->deleteJson("/api/events/{$event->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Event not found.',
        ]);
    }

    public function test_event_store_rejects_application_from_another_user(): void
    {
        $owner = User::factory()->create();
        $foreignApplication = Application::factory()->create(['user_id' => $owner->id]);
        $this->authenticateUser();

        $response = $this->postJson('/api/events', [
            'application_id' => $foreignApplication->id,
            'title' => 'Cross-tenant interview',
            'event_type' => 'interview',
            'event_date' => now()->addDay()->toDateString(),
            'is_all_day' => false,
            'event_time' => '10:00',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['application_id']);
    }
}
