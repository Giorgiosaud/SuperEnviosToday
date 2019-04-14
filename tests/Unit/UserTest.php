<?php

namespace Tests\Unit;

use App\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit
 */
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
        $user = factory(User::class)->create([
            'name' => 'ALEX',
            'email' => 'A@be.com',
            'password' => bcrypt('LIN'),
        ]);
        $user->refresh();
        $this->assertTrue($user->hasRole('client'));
    }

    /**
     * ActingAsCoordinatorHaveAPasswordToAccess.
     *
     * @test
     */
    public function theCoordinatorCanLoginWithHisPassword()
    {
        $user = factory(User::class)->create([
            'name' => 'Coordinador',
            'idn' => '111111',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->toogleRole('coordinator');
        $user->refresh();
        $this->assertTrue($user->hasRole('coordinator'));
        $response = $this->post('login', ['idn_type' => 'CI', 'idn' => '111111', 'password' => 'hidden']);
        $response->assertRedirect('/home');
        $this->isAuthenticated();
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
        factory('App\Account', 3)->create(['user_id' => $user->id]);
        $this->assertCount(3, $user->accounts);
    }

    /**
     * @test
     */
    public function aUserWithoutEmailCanBeRegistered()
    {
        $user = factory('App\User')->create(['email' => null]);
        $this->assertNull(User::find($user->id)->email);


    }

    /**
     * @test
     */
    public function aUserWithDuplicatedEmailCanBeRegistered()
    {
        $email='test@test.com';
        factory('App\User')->create(['name'=>'test1','email'=>$email]);
        factory('App\User')->create(['name'=>'test2','email'=>$email]);
        $users=User::all();
        $this->assertCount(2,$users);
        $this->assertEquals($users[0]->email,$users[1]->email);

    }
    /**
     * @test
     */
    public function aUserCantBeRegisteredWithSameCombinationOfIDNandIDNTYPE()
    {
        try {
            factory('App\User')->create(['idn'=>'123123','idn_type'=>'PASSPORT']);
            factory('App\User')->create(['idn'=>'123123','idn_type'=>'PASSPORT']);
        } catch (Exception $err) {
            $this->assertContains('Integrity constraint violation', $err->getMessage());
        }
    }

    /**
     * @test
     */
    public function aUserCanRegisterAReceiverAndAsociateIt(){
        $user=factory('App\User')->create();
        $related=factory('App\User')->create();
        $user->receivers()->attach($related->id);
        $related=factory('App\User')->create();
        $user->receivers()->attach($related->id);
        $this->assertCount(2,$user->receivers);
    }

    /**
     * @test
     */
    public function aReceiverUserCanHaveManyAsociatedSenders(){
        $user=factory('App\User')->create();
        $related=factory('App\User')->create();
        $related->senders()->attach($user->id);
        $user2=factory('App\User')->create();
        $user2->receivers()->attach($related->id);
        $this->assertCount(2,$related->senders);
    }

}
