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
      $user=factory(User::class)->create([
        'name'     => 'Alejandro',
        'email'    => 'alejandro@ronpapas.com',
        'password' => bcrypt('Ronpapa'),
      ]);
      $user->toogleRole('coordinator');
      $user = factory(User::class)->create([
        'name'     => 'Jorge',
        'email'    => 'jorgelsaud@gmail.com',
        'phone'  => '+56952218734',
        'password' => bcrypt('17762267'),
      ]);
      $user->toogleRole('coordinator');
      $user = factory(User::class)->create([
        'name'     => 'Cliente',
        'email'    => 'cliente@cliente.com',
        'password' => bcrypt('secret'),
      ]);
    }
  }
