<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /* //Tailored method to set up the testing enviroment.
    public function test_set_database_config(){
            Artisan::call('migrate:reset');
            Artisan::call('migrate');
            Artisan::call('db:seed');
            Artisan::call('passport:install');

            $response = $this->get('/');
            $response->assertStatus(200);
        } */

    //Using the built in method to set up the testing enviroment.  
    public function setUp (): void {

        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');

    }

    public function test_register_all_data_correct(): void
    {
        $userData = [
            'name' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->email,
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->postJson('/api/players', $userData);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'User created successfully', 'status' => 'success']);
        
    }

    public function test_register_with_data_mismatch_validation_error(): void
    {
        $userData = [
            'name' => $this->faker->unique()->userName,
            'email' => 'kjhcalcbl',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->postJson('/api/players', $userData);
        $response->assertJson(['message' => 'Validation Error!', 'status' => 'failed']);
        $response->assertStatus(403);
    }
}
