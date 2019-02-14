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
            'name_id' => 'coordinator',
            'name'=>'Coordinador'
        ]);
        Role::create([
            'name_id' => 'chilean_operator',
            'name'=>'Operador Chileno'
        ]);
        Role::create([
            'name_id' => 'venezuelan_operator',
            'name'=>'Operador Venezolano'
        ]);
        Role::create([
            'name_id' => 'client',
            'name'=>'Cliente'
        ]);
        Role::create([
            'name_id' => 'receiver',
            'name'=>'Receptor'
        ]);
    }
}
