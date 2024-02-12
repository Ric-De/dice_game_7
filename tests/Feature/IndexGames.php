<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;

class IndexGamesTest extends TestCase
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

    public function test_index_games(): void
    {
        $user = User::factory()->create();
        $user->assignRole('player');
    
        //Create games for the user
        $games = Game::factory()->count(10)->create(['user_id' => $user->id]);
    
        $this->actingAs($user, 'api');
    
        //Call the index_games method
        $response = $this->json('GET', "/api/players/{$user->id}/games");
    
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'Number of games' => [
                '*' => ['Dice 1', 'Dice 2', 'Result'],
            ],
            'Success Percentage Rate',
            
        ]);
    
    }

    public function test_index_games_with_wrong_role(){

        $user = User::factory()->create();
        $user->assignRole('admin');
        $games = Game::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->Json('GET', "/api/players/{$user->id}/games");
        $response->assertStatus(403);
    }

    public function test_index_games_with_non_authenticated_user(){

        $user = User::factory()->create();
        $games = Game::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->Json('GET', "/api/players/{$user->id}/games");
        $response->assertStatus(401);
    }
}