<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    public function setUp (): void {

        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');

    }

    //This test checks logout endpoint can deal with a request with a valid access token, revoke the token, and respond with a success status.
    public function test_logout_with_all_data_correct(){

        $loginUserData = User::factory()->create();

        //Checking if the user has a token
        $this->assertCount(0, $loginUserData->tokens);

        $accessToken = $loginUserData->createToken('testToken')->accessToken;

        //Checking if the user is capable of access the URL with token and verify the asserts.
        $response = $this->withToken($accessToken)->post('api/logout');
        $response->assertJson(['message' => 'User logged out', 'status' => true]);
        $response->assertStatus(200);
    }

}
