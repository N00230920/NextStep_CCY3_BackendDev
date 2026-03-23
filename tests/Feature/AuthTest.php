<?php

namespace Tests\Feature;

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
    /**
     * A basic feature test example.
     */

    public function test_user_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);

        $this->assertEquals($response->json('name'), $user->name);
        $this->assertEquals($response->json('email'), $user->email);
    }

    
    public function test_user_register(): void
    {
        $user = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $user);

        $response->assertStatus(201);
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
        $this->assertEquals('User registered successfully', $message);
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

        $response = $this->postJson('/api/register', $user);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data',
            'message',
            'success'
        ]);

        $success = $response->json('success');
        $message = $response->json('message');

        $this->assertEquals($success, false);
        $this->assertEquals($message, 'Validation Error');

        $this->assertDatabaseMissing('users', [
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }

    public function test_user_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_login_error(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


}
