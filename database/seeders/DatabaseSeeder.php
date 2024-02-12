<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Game;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Populating game and user tables with fakers. Calling the RoleSeeder.
        User::factory(10)->create();
        Game::factory(10)->create();
        $this->call(RoleSeeder::class);
        
        //Creating a default Admin
        $defaultAdmin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt(123456789),
            'remember_token' => Str::random(10),
        ]);

        $defaultAdmin->assignRole('admin');

        //Creating a default Player
        $defaultPlayer = User::create([
            'name' => 'player',
            'email' => 'player@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt(987654321),
            'remember_token' => Str::random(10),
        ]);

        $defaultPlayer->assignRole('player');


    }
}
