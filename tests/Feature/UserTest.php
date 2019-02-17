<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class UserTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function aUsersAPIWORKS()
    {
        $this->disableExceptionHandling();
        factory(User::class,10)->create();
        $user = factory(User::class)->create([
            'name' => 'Coordinador',
            'idn' => '1',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('coordinator');
        Passport::actingAs(
            $user,
            ['create-servers']
        );

        $response = $this->get('/api/users');
        $response->assertJsonCount(11, $key = 'data');

    }

    /**
     * @test
     */
    public function rolesAPIWorks(){
        $user = factory(User::class)->create([
            'name' => 'Coordinador',
            'idn' => '1',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('coordinator');
        Passport::actingAs(
            $user,
            ['create-servers']
        );
        $response = $this->get('/api/roles');
        $response->assertJsonCount(4, $key = 'data');

        $response->assertStatus(200);

    }
}

