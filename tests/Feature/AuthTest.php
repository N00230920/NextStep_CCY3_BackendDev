<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals($response->json('data.name'), $user->name);
        $this->assertEquals($response->json('data.email'), $user->email);
    }

    
    public function test_user_register(): void
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $user);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token', 'name'
            ],
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $token = $response->json('data.token');
        $name = $response->json('data.name');

        $this->assertEquals(true, $success);
        $this->assertEquals('User registered successfully.', $message);
        $this->assertNotNull($token);
        $this->assertEquals($user['name'], $name);
        
        $this->assertDatabaseHas('users', [
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }

    public function test_user_register_error(): void
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ];

        User::factory()->create([
            'email' => $user['email'],
        ]);

        $response = $this->postJson('/api/register', $user);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'email'
            ],
        ]);

        $message = $response->json('message');
        $errors = $response->json('data.email');

        $this->assertEquals('Validation Error', $message);
        $this->assertContains('The email has already been taken.', $errors);
    }

    public function test_user_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token', 'name'
            ],
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');
        $token = $response->json('data.token');
        $name = $response->json('data.name');

        $this->assertEquals(true, $success);
        $this->assertEquals('User logged in successfully.', $message);
        $this->assertNotNull($token);
        $this->assertEquals($user->name, $name);
    }

    public function test_user_login_error(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'data',
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');

        $this->assertEquals(false, $success);
        $this->assertEquals('Invalid credentials.', $message);
    }

}
