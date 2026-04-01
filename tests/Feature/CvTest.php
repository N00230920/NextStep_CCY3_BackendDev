<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CvTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_cv_index(): void
    {
        $user = $this->authenticateUser();
        Cv::factory()->count(2)->create(['user_id' => $user->id]);
        Cv::factory()->create();

        $response = $this->getJson('/api/cvs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'items' => [
                    '*' => [
                        'id',
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'location',
                        'links',
                        'bio',
                        'experience',
                        'education',
                        'skills',
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
        $response->assertJsonCount(2, 'data.items');
    }

    public function test_cv_show(): void
    {
        $user = $this->authenticateUser();
        $cv = Cv::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/cvs/{$cv->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'name',
                'email',
                'phone',
                'location',
                'links',
                'bio',
                'experience',
                'education',
                'skills',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertEquals($cv->id, $response->json('data.id'));
    }

    public function test_cv_show_not_found(): void
    {
        $this->authenticateUser();
        $response = $this->getJson('/api/cvs/999');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
        $this->assertEquals('CV not found', $response->json('message'));
        $this->assertEquals(false, $response->json('success'));
    }

    public function test_cv_store(): void
    {
        $this->authenticateUser();

        $cvData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '07123456789',
            'location' => $this->faker->city(),
            'links' => 'https://example.com',
            'bio' => $this->faker->text(),
            'experience' => $this->faker->sentence(),
            'education' => $this->faker->sentence(),
            'skills' => $this->faker->word(),
        ];

        $response = $this->postJson('/api/cvs', $cvData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'name',
                'email',
                'phone',
                'location',
                'links',
                'bio',
                'experience',
                'education',
                'skills',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertDatabaseHas('cvs', [
            'name' => $cvData['name'],
            'email' => $cvData['email'],
        ]);
    }

    public function test_cv_store_validation_error(): void
    {
        $this->authenticateUser();
        $response = $this->postJson('/api/cvs', []);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
        $this->assertEquals('The name field is required.', $response->json('errors.name.0'));
        $this->assertEquals('The email field is required.', $response->json('errors.email.0'));
        $this->assertEquals('The phone field is required.', $response->json('errors.phone.0'));
    }

    public function test_cv_update(): void
    {
        $user = $this->authenticateUser();
        $cv = Cv::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '07123456789',
            'location' => $this->faker->city(),
            'links' => 'https://example.com',
            'bio' => $this->faker->text(),
            'experience' => $this->faker->sentence(),
            'education' => $this->faker->sentence(),
            'skills' => $this->faker->word(),
        ];

        $response = $this->putJson("/api/cvs/{$cv->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'name',
                'email',
                'phone',
                'location',
                'links',
                'bio',
                'experience',
                'education',
                'skills',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertDatabaseHas('cvs', [
            'id' => $cv->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_cv_update_validation_error(): void
    {
        $user = $this->authenticateUser();
        $cv = Cv::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/cvs/{$cv->id}", []);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors',
        ]);
        $this->assertArrayHasKey('name', $response->json('errors'));
        $this->assertArrayHasKey('email', $response->json('errors'));
    }

    public function test_cv_update_not_found_error(): void
    {
        $this->authenticateUser();
        $response = $this->putJson('/api/cvs/999', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '07123456789',
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
        $this->assertEquals('CV not found', $response->json('message'));
    }

    public function test_cv_destroy(): void
    {
        $user = $this->authenticateUser();
        $cv = Cv::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/cvs/{$cv->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'success',
        ]);
        $this->assertEquals('CV deleted successfully', $response->json('message'));
        $this->assertEquals(true, $response->json('success'));
        $this->assertDatabaseMissing('cvs', [
            'id' => $cv->id,
        ]);
    }

    public function test_cv_destroy_not_found_error(): void
    {
        $this->authenticateUser();
        $response = $this->deleteJson('/api/cvs/999');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
        $this->assertEquals('CV not found', $response->json('message'));
    }

    public function test_cv_user_cannot_access_another_users_cv(): void
    {
        $owner = User::factory()->create();
        $cv = Cv::factory()->create(['user_id' => $owner->id]);
        $this->authenticateUser();

        $response = $this->getJson("/api/cvs/{$cv->id}");
        $response->assertStatus(404);
        $this->assertEquals('CV not found', $response->json('message'));

        $response = $this->putJson("/api/cvs/{$cv->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '07123456789',
        ]);
        $response->assertStatus(404);
        $this->assertEquals('CV not found', $response->json('message'));

        $response = $this->deleteJson("/api/cvs/{$cv->id}");
        $response->assertStatus(404);
        $this->assertEquals('CV not found', $response->json('message'));
    }
}
