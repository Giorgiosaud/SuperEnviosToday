<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(User::all()->count()==0) {

            $user=User::factory()->create([
                'name' => 'Jorge',
                'last_name' => 'Saud',
                'idn' => '17762267',
                'idn_type' => 'CI',
                'email' => 'jorgelsaud@gmail.com',
                'password' => bcrypt('mypassword')
            ]);
            $user->setRole('coordinator');

        }
        //
    }
}
