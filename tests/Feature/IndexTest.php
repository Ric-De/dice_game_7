<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class IndexTest extends TestCase
{
   
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');
    }

    public function test_index_method_with_right_role_admin(): void
    {
       
        $user = User::factory()->create();
        $user->assignRole('admin'); 
        
        //Act
        $response = $this->actingAs($user, 'api')->Json('GET', '/api/players');

        //Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users' => [
                '*' => [
                    'message',
                    'Id',
                    'Name',
                    'E-mail',
                    'Success Rate',
                ],
            ],
        ]);
    }

    public function test_index_method_with_wrong_role_player() {
        
        $user = User::factory()->create();
        $user->assignRole('player');

        $response = $this->actingAs($user, 'api')->Json('GET', '/api/players/');
        $response->assertStatus(403);
    }


}
