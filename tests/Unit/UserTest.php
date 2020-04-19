<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // you can call
        $this->artisan('db:seed');

        // or
        $this->seed();
    }
    /**
     * A basic test example.
     *
     * @return void
     * @test
     * */
    public function aUserHaveADefaultRoleOfClient()
    {
        $user = factory(User::class)->create([
            'name'     => 'ALEX',
            'email'    => 'A@be.com',
            'password' => bcrypt('hidden'),
        ]);
        $this->assertTrue($user->hasRole('client'));
    }

    /**
     * ActingAsCoordinatorHaveAPasswordToAccess.
     *
     * @test
    public function theCoordinatorCanLoginWithHisPassword()
    {
        $user = factory(User::class)->create([
            'name'     => 'Coordinador',
            'idn'      => '111111',
            'idn_type' => 'CI',
            'password' => bcrypt('hidden'),
        ]);
        $user->setRole('coordinator');
        $user->refresh();
        $this->assertTrue($user->hasRole('coordinator'));
        $response = $this->post('login', ['idn_type' => 'CI', 'idn' => '111111', 'password' => 'hidden']);
        $response->dump();
        $response->assertRedirect('/home');
        $this->isAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * A user can have accounts associated.
     *
     * @test
    public function aUserHaveMultiplesAccountsAssociated()
    {
        $user = factory('App\User')->create();
        $bank=factory('App\Bank')->create();
        $account=factory('App\Account')->make();
        $userAccount=[
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'type'    => $account->type,
            'number'    => $account->number,
        ];
        $account=factory('App\Account')->make();

        $this->postJson(route('save_account'), $userAccount)->assertStatus(401);
        $this->actingAsForeignOperator();
        $this->postJson(route('save_account'), $userAccount)->assertStatus(201);
        $userAccount=[
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'type'    => $account->type,
            'number'    => $account->number,
        ];
        $this->postJson(route('save_account'), $userAccount)->assertStatus(201);
        $user->refresh();
        $this->assertCount(2,$user->accounts);
    }

    /**
     * @test
    public function aUserWithoutEmailCanBeRegistered()
    {
        $user = factory('App\User')->create(['email' => null]);
        $this->assertNull(User::find($user->id)->email);
    }

    /**
     * @test
    public function aUserWithDuplicatedEmailCanBeRegistered()
    {
        $email = 'test@test.com';
        factory('App\User')->create(['name'=>'test1', 'email'=>$email]);
        factory('App\User')->create(['name'=>'test2', 'email'=>$email]);
        $users = User::all();
        $this->assertCount(2, $users);
        $this->assertEquals($users[0]->email, $users[1]->email);
    }

    /**
     * @test
    public function aUserCantBeRegisteredWithSameCombinationOfIDNandIDNTYPE()
    {
        try {
            factory('App\User')->create(['idn'=>'123123', 'idn_type'=>'PASSPORT']);
            factory('App\User')->create(['idn'=>'123123', 'idn_type'=>'PASSPORT']);
        } catch (Exception $err) {
            $this->assertContains('Integrity constraint violation', $err->getMessage());
        }
    }

    /**
     * @test
    public function aUserCanRegisterAReceiverAndAsociateIt()
    {
        $user = factory('App\User')->create();
        $related = factory('App\User')->create();
        $user->receivers()->attach($related->id);
        $related = factory('App\User')->create();
        $user->receivers()->attach($related->id);
        $this->assertCount(2, $user->receivers);
    }

    /**
     * @test
    public function aReceiverUserCanHaveManyAsociatedSenders()
    {
        $user = factory('App\User')->create();
        $related = factory('App\User')->create();
        $related->senders()->attach($user->id);
        $user2 = factory('App\User')->create();
        $user2->receivers()->attach($related->id);
        $this->assertCount(2, $related->senders);
    }
     *      */

}
