<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Role;

class UpdateTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function setUp (): void {

        parent::setUp();

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('passport:install');

    }

    public function test_update_user(): void{

        // Create a user
        $user = User::factory()->create();
        $user->assignRole('player'); 

        $newname = 'newname';

        //Act
        User::where('id', $user->id)->update(['name' => $newname]);

        //Fetch the user again after the update
        $updatedUser = User::find($user->id);

        $response = $this->actingAs($user, 'api')->Json('PUT', "/api/players/{$user->id}", ['name' => $newname]);

        //Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Name update completed']);
        
        //Check if the user retrieved after the update has the expected name
        $this->assertEquals($newname, $updatedUser->name);

    }

}