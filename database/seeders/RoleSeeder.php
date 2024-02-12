<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $playerRole = Role::create(['name' => 'player']);
        
        //Admin Permissions
        Permission::create(['name'=>'index'])->syncRoles([$adminRole]);
        Permission::create(['name'=>'getRanking'])->syncRoles([$adminRole]);
        Permission::create(['name'=>'getLoser'])->syncRoles([$adminRole]);
        Permission::create(['name'=>'getWinner'])->syncRoles([$adminRole]);
               
        //Player Permissions
        Permission::create(['name'=>'index_games'])->syncRoles([$playerRole]);
        Permission::create(['name'=>'play'])->syncRoles([$playerRole]);
        Permission::create(['name'=>'update'])->syncRoles([$playerRole]);
        Permission::create(['name'=>'destroy'])->syncRoles([$playerRole]);

    }

}

?>
