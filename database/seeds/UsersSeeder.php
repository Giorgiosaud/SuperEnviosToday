<?php

use App\User;
use Carbon\Carbon;
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
    User::unguard();
    $user = User::create([
      'name' => 'Alejandro',
      'email' => 'alejandro@ronpapas.com',
      'password' => bcrypt('Ronpapa'),
    ]);
    $user->toogleRole('coordinator');
    $user = User::create([
      'name' => 'Jorge',
      'email' => 'jorgelsaud@gmail.com',
      'password' => bcrypt('17762267'),
    ]);
    $user->toogleRole('coordinator');
    User::reguard();
  }
}