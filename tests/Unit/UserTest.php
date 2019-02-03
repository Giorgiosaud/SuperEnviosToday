<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     * @test
     */
    public function aUserHaveADefaultRoleOfClient()
    {
        $user = User::create([
            'name'     => 'ALEX',
            'email'    => 'A@be.com',
            'password' => bcrypt('LIN'),
        ]);
        $this->assertTrue($user->hasRole('client'));
    }

    /**
     * ActingAsCoordinatorHaveAPasswordToAccess.
     *
     * @test
     */
    public function theCoordinatorCanLoginWithHisPassword()
    {
        $this->disableExceptionHandling();
        $user = User::whereName('Alejandro')->first();
        $this->assertTrue($user->hasRole('coordinator'));
        $response = $this->post('login', ['email' => 'alejandro@ronpapas.com', 'password' => 'Ronpapa']);
        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * A user can have accounts asociated.
     *
     * @test
     */
    public function aUserHaveMultiplesAccountsAsociated()
    {
        $user = factory('App\User')->create();
        $accounts = factory('App\Account', 3)->create(['user_id' => $user->id]);
        $this->assertCount(3, $user->accounts);
    }
}
