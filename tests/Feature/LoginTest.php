<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
 

class LoginTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    public function setUp (): void {

        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');

    }

    public function test_login_all_data_correct() {

        $loginUserData = User::factory()->create([
            'email' => 'juventino@gmail.com',
            'password' => bcrypt('pass987'),
        ]);

        $loginUserData = [
            'email' => 'juventino@gmail.com',
            'password' => 'pass987',
        ];
      
        $response = $this->Json('POST', '/api/login', $loginUserData);
        $response->assertStatus(200);
    
    
    }

    public function test_login_with_data_mismath_validation_error() {

        $loginUserData = User::factory()->create([
            'email' => 'juventino@gmail.com',
            'password' => bcrypt('pass987'),
        ]);

        $loginUserData = [
            'email' => 'juventino@gmail.com',
            'password' => 'incorrect password',
        ];
      
        $response = $this->Json('POST', '/api/login', $loginUserData);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid credentials', 'status' => 'failed']);
        
    }

}
