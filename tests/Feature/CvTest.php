<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CvTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_cv_index (): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_cv_show (): void
    {
        $cv = Cv::factory()->create();

        $response = $this->get("/cvs/{$cv->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'phone',
            'address',
            'location',
            'links',
            'bio',
            'experience',
            'education',
            'skills',
            'created_at',
            'updated_at'
        ]);
    }

    public function test_cv_show_not_found (): void
    {
        $response = $this->get('/cvs/999');

        $response->assertStatus(404);
    }

    public function test_cv_store():void 
    {

    }

    public function test_cv_store_validation_error():void 
    {

    }

    public function test_cv_update():void 
    {

    }

    public function test_cv_update_validation_error():void 
    {

    }

    public function test_cv_update_not_found_error():void 
    {

    }

    public function test_cv_destroy():void 
    {

    }

    public function test_cv_destroy_not_found_error():void 
    {

    }


}
