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
            'idn' => '123',
            'idn_type' => 'PASSPORT',
            'password' => bcrypt('123'),
        ]);
        $receiver = factory(User::class)->create([
            'idn' => '123',
            'idn_type' => 'CI',
            'password' => bcrypt('123'),
        ]);
        $receiver->senders()->attach($client->id);
        $user = factory(User::class)->create([
            'name' => 'Alejandro',
            'idn' => '123123123',
            'idn_type' => 'PASSPORT',
            'last_name' => 'Ronpapas',
            'email' => 'alejandro@ronpapas.com',
            'password' => bcrypt('Ronpapas'),
        ]);
        $user->toogleRole('coordinator');
        $user = factory(User::class)->create([
            'idn_type' => 'RUT',
            'idn' => '263215982',
            'name' => 'Jorge',
            'last_name' => 'Saud',
            'email' => 'jorgelsaud@gmail.com',
            'phone' => '+56952218734',
            'password' => bcrypt('17762267'),
        ]);
        $user->toogleRole('coordinator');
        $user = factory(User::class)->create([
            'idn_type' => 'RUT',
            'idn' => '123',
            'name' => 'Operador Chile ',
            'last_name' => 'Prueba'
        ]);
        $user->toogleRole('chilean_operator');
        $user = factory(User::class)->create([
            'idn_type' => 'RUT',
            'idn' => '19',
            'name' => 'Operador Venezuela ',
            'last_name' => 'Prueba'
        ]);
        $user->toogleRole('venezuelan_operator');
        factory(User::class)->create();
    }
}
