<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Cover;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CoverTest extends TestCase
{
    
    use RefreshDatabase;
    use WithFaker;

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_cover_index(): void
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/covers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'user_id',
                        'application_id',
                        'title',
                        'content',
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

    public function test_cover_show(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        $cover = Cover::factory()->create(['user_id' => $user->id, 'application_id' => $application->id]);

        $response = $this->getJson("/api/covers/{$cover->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cover letter retrieved successfully',
            'data' => [
                'id' => $cover->id,
                'user_id' => $cover->user_id,
                'application_id' => $cover->application_id,
                'title' => $cover->title,
                'content' => $cover->content,
            ],
        ]);
    }

    public function test_cover_show_not_found(): void 
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/covers/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);
    }

    public function test_cover_store(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        $data = [
            'application_id' => $application->id,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson('/api/covers', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'application_id',
                'title',
                'content',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_cover_store_validation_error():void 
    {
        $this->authenticateUser();
        $response = $this->postJson('/api/covers', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content']);
    } 

    public function test_cover_update(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        $cover = Cover::factory()->create(['user_id' => $user->id, 'application_id' => $application->id]);

        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated content for the cover letter.',
        ];

        $response = $this->putJson("/api/covers/{$cover->id}", $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cover letter updated successfully',
            'data' => [
                'id' => $cover->id,
                'user_id' => $cover->user_id,
                'application_id' => $cover->application_id,
                'title' => 'Updated Title',
                'content' => 'Updated content for the cover letter.',
            ],
        ]);
    }

    public function test_cover_update_validation_error(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        $cover = Cover::factory()->create(['user_id' => $user->id, 'application_id' => $application->id]);

        $response = $this->putJson("/api/covers/{$cover->id}", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_cover_update_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->putJson('/api/covers/999', [
            'title' => 'Updated Title',
            'content' => 'Updated content for the cover letter.',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);
    }

    public function test_cover_destroy(): void
    {
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);
        $cover = Cover::factory()->create(['user_id' => $user->id, 'application_id' => $application->id]);

        $response = $this->deleteJson("/api/covers/{$cover->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Cover letter deleted successfully',
        ]);
        $this->assertDatabaseMissing('covers', [
            'id' => $cover->id,
        ]);
    }

    public function test_cover_destroy_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->deleteJson('/api/covers/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);

    }

    public function test_cover_user_cannot_access_another_users_cover(): void
    {
        $owner = User::factory()->create();
        $ownerApplication = Application::factory()->create(['user_id' => $owner->id]);
        $cover = Cover::factory()->create([
            'user_id' => $owner->id,
            'application_id' => $ownerApplication->id,
        ]);
        $this->authenticateUser();

        $response = $this->getJson("/api/covers/{$cover->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);

        $response = $this->putJson("/api/covers/{$cover->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);

        $response = $this->deleteJson("/api/covers/{$cover->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Cover letter not found',
        ]);
    }

}
