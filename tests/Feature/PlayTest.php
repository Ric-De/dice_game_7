<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class PlayTest extends TestCase
{
    public function test_play()
    {
        $player = User::factory()->create();
        $player->assignRole('player');
        Passport::actingAs($player);
    
        $response = $this->Json('POST', '/api/players/' . $player->id . '/games');
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'message',
            'Dice 1',
            'Dice 2',
            'Sum',
            'Result',
        ]);
    }

    public function test_play_with_wrong_role(){

        $user = User::factory()->create();
        $user->assignRole('admin');
        $response = $this->actingAs($user, 'api')->Json('GET', "/api/players/{$user->id}/games");
        $response->assertStatus(403);
    }
}
