<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class GetWinnerTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;
    
    public function setUp (): void {

        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');

    }

    public function test_winner(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $response = $this->Json('GET', '/api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'Name',
            'Wins Rate',
            'Wins',
            'Total Games',
        ]);

    }

}
