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
        if(Role::all()->count()==0) {
            Role::create([
                'name_id' => 'coordinator',
                'name' => 'Coordinador',
            ]);
            Role::create([
                'name_id' => 'foreign_operator',
                'name' => 'Operador Extranjero',
            ]);
            Role::create([
                'name_id' => 'venezuelan_operator',
                'name' => 'Operador Venezolano',
            ]);
            Role::create([
                'name_id' => 'client',
                'name' => 'Cliente',
            ]);
            Role::create([
                'name_id' => 'receiver',
                'name' => 'Receptor',
            ]);
        }
    }
}
