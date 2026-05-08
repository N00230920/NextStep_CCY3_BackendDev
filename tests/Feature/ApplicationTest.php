<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Application;
use App\Models\Cv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function authenticateUser(): User
    {
        // Create a user and authenticate with Sanctum for API testing
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_application_index(): void
    {
        // Create some applications that belong to the authenticated user and attempt to list them
        $this->authenticateUser();
        $response = $this->getJson('/api/applications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'user_id',
                        'cv_id',
                        'company_name',
                        'status',
                        'job_type',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    public function test_application_show(): void
    {
        // Create an application that belongs to the authenticated user and attempt to show it
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/applications/{$application->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'cv_id',
                'company_name',
                'position',
                'location',
                'contact_email',
                'salary',
                'status',
                'job_type',
                'job_url',
                'notes',
                'applied_date',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_application_show_not_found(): void
    {
        // Attempt to show an application that doesn't exist
        $this->authenticateUser();
        $response = $this->getJson('/api/applications/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);
    }

    public function test_application_store(): void
    {
        // Create a CV that belongs to the authenticated user to associate with the application
        $user = $this->authenticateUser();
        $cv = Cv::factory()->create(['user_id' => $user->id]);

        $data = [
            'cv_id' => $cv->id,
            'company_name' => $this->faker->company,
            'status' => 'applied',
            'job_type' => 'full-time',
            'position' => $this->faker->jobTitle,
            'location' => $this->faker->city,
            'contact_email' => $this->faker->email,
            'salary' => $this->faker->randomNumber(6),
            'job_url' => $this->faker->url,
            'notes' => $this->faker->sentence,
            'applied_date' => $this->faker->date,
        ];

        $response = $this->postJson('/api/applications', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'cv_id',
                'company_name',
                'position',
                'location',
                'contact_email',
                'salary',
                'status',
                'job_type',
                'job_url',
                'notes',
                'applied_date',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'cv_id' => $cv->id,
            'company_name' => $data['company_name'],
            'position' => $data['position'],
            'status' => $data['status'],
            'job_type' => $data['job_type'],
        ]);
    }

    public function test_application_store_validation_error(): void
    {
        // Attempt to create an application with missing required fields
        $this->authenticateUser();
        $response = $this->postJson('/api/applications', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['company_name', 'position', 'status']);
    }

    public function test_application_update(): void
    {
        // Create an application that belongs to the authenticated user and attempt to update it
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);

        $data = [
            'company_name' => 'Updated Company',
            'position' => 'Updated Position',
            'status' => 'interview',
        ];

        $response = $this->putJson("/api/applications/{$application->id}", $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Application updated successfully',
            'data' => [
                'id' => $application->id,
                'company_name' => 'Updated Company',
                'position' => 'Updated Position',
                'status' => 'interview',
            ],
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'company_name' => 'Updated Company',
            'position' => 'Updated Position',
            'status' => 'interview',
        ]);
    }

    public function test_application_update_validation_error(): void
    {
        // Create an application that belongs to the authenticated user and attempt to update it with invalid data
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/applications/{$application->id}", [
            'company_name' => '',
            'position' => '',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['company_name', 'position', 'status']);
    }

    public function test_application_update_not_found(): void
    {
        // Attempt to update an application that doesn't exist
        $this->authenticateUser();
        $response = $this->putJson('/api/applications/999', [
            'company_name' => 'Updated Company',
            'position' => 'Updated Position',
            'status' => 'interview',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);
    }

    public function test_application_delete(): void
    {
        // Create an application that belongs to the authenticated user and attempt to delete it
        $user = $this->authenticateUser();
        $application = Application::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/applications/{$application->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Application deleted successfully',
        ]);

        $this->assertDatabaseMissing('applications', ['id' => $application->id]);
    }

    public function test_application_delete_not_found(): void
    {
        // Attempt to delete an application that doesn't exist
        $this->authenticateUser();
        $response = $this->deleteJson('/api/applications/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);
    }

    public function test_application_endpoints_require_authentication(): void
    {
        // Test index and store endpoints without authentication
        $response = $this->getJson('/api/applications');
        $response->assertStatus(401);

        $response = $this->postJson('/api/applications', [
            'company_name' => 'Test Company',
            'position' => 'Engineer',
            'status' => 'applied',
        ]);
        $response->assertStatus(401);
    }

    public function test_application_user_cannot_access_another_users_application(): void
    {
        // Create an application that belongs to another user and attempt to access it with the authenticated user
        $owner = User::factory()->create();
        $application = Application::factory()->create(['user_id' => $owner->id]);
        $this->authenticateUser();

        $response = $this->getJson("/api/applications/{$application->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);

        $response = $this->putJson("/api/applications/{$application->id}", [
            'company_name' => 'Updated Company',
            'position' => 'Updated Position',
            'status' => 'interview',
        ]);
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);

        $response = $this->deleteJson("/api/applications/{$application->id}");
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Application not found',
        ]);
    }

    public function test_application_store_rejects_cv_from_another_user(): void
    {
        $owner = User::factory()->create();
        // Create a CV that belongs to another user
        $foreignCv = Cv::factory()->create(['user_id' => $owner->id]);
        $this->authenticateUser();

        $response = $this->postJson('/api/applications', [
            'cv_id' => $foreignCv->id,
            'company_name' => 'Tenant Safe Inc',
            'position' => 'Backend Engineer',
            'status' => 'applied',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cv_id']);
    }
}
