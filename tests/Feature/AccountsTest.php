<?php

namespace Tests\Feature;

use App\Account;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class AccountsTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function aUsersAPIWORKS()
    {
        factory(User::class, 10)->create();
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
        $user->refresh();
        $response = $this->get('/api/users');
        $response->assertJsonCount(11, $key = 'data');

    }

    /**
     * @test
     */
    public function onlyCoordinatorAndForeignOperatorCanSaveAccounts(){
        $account=factory(Account::class)->make();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(401);
        $this->actingAsReceiver();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
        $this->actingAsClient();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
        $this->actingAsVenezuelanOperator();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
        $this->actingAsForeignOperator();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(201);
        $account=factory(Account::class)->make();
        $this->actingAsCoordinator();
        $this->postJson(route('save_account'), $account->toArray())->assertStatus(201);
    }

    /**
     * @test
     */
    public function onlyCoordinatorCanSaveOperatorAccounts(){
        $account=factory(Account::class)->make(['is_operator_account'=>true]);
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(401);
        $this->actingAsReceiver();
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(403);
        $this->actingAsClient();
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(403);
        $this->actingAsVenezuelanOperator();
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(403);
        $this->actingAsForeignOperator();
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(403);
        $this->actingAsCoordinator();
        $this->postJson(route('save_operator_account'), $account->toArray())->assertStatus(201);
    }
}

