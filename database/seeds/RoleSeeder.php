<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'coordinator',
        ]);
        Role::create([
            'name' => 'chilean_operator',
        ]);
        Role::create([
            'name' => 'venezuelan_operator',
        ]);
        Role::create([
            'name' => 'client',
        ]);
    }
}
