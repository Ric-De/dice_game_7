<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;

class GetRankingTest extends TestCase{

    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');
    }

    public function test_getRanking_method_with_right_role_admin(): void
    {
        //Create a user
        $user = User::factory()->create();
        $user->assignRole('admin');

        //Act
        $response = $this->actingAs($user, 'api')->Json('GET', '/api/players/ranking');

        //Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'message',
                'Name',
                'Win Rate',
                'Wins',
                'Total Games',
            ],
        ]);
    }

}
