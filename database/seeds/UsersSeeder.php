<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = factory(User::class)->create([
            'idn' => '17762267',
            'idn_type' => 'CI',
            'password' => bcrypt('123'),
        ]);
        $receiver = factory(User::class)->create([
            'idn' => 'J-09513132-7',
            'idn_type' => 'RIF',
            'password' => bcrypt('123'),
        ]);
        $receiver->senders()->attach($client->id);
        $user = factory(User::class)->create([
            'idn_type' => 'RUT',
            'idn' => '263215982',
            'name' => 'Jorge',
            'last_name' => 'Saud',
            'email' => 'jorgelsaud@gmail.com',
            'phone' => '+56952218734',
            'password' => bcrypt('17762267'),
        ]);
        $user->setRole('coordinator');
        $user = factory(User::class)->create([
            'idn_type' => 'RUT',
            'idn' => '123',
            'name' => 'Operador Chile ',
            'last_name' => 'Prueba',
            'password' => bcrypt('secret'),

        ]);
        $user->setRole('foreign_operator');
        $user = factory(User::class)->create([
            'idn_type' => 'CI',
            'idn' => '19',
            'name' => 'Operador Venezuela Name',
            'last_name' => 'Last Name',
            'password' => bcrypt('secret'),
        ]);
        $user->setRole('venezuelan_operator');
    }
}
