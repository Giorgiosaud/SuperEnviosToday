<?php

namespace Tests\Feature;

use App\Account;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserTest.
 */
  class AccountsTest extends TestCase
  {
      use RefreshDatabase;

      /**
       * A basic test example.
       *
       * @test
       *
       * @return void
       */
      public function aUsersAPIWORKS()
      {
          factory(User::class, 10)->create();
          $user = factory(User::class)->create([
        'name'     => 'Coordinador',
        'idn'      => '1',
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
      public function onlyCoordinatorAndForeignOperatorCanSaveAccounts()
      {
          $account = factory(Account::class)->make();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(401);
          $this->actingAsReceiver();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
          $this->actingAsClient();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
          $this->actingAsVenezuelanOperator();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(403);
          $this->actingAsForeignOperator();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(201);
          $account = factory(Account::class)->make();
          $this->actingAsCoordinator();
          $this->postJson(route('save_account'), $account->toArray())->assertStatus(201);
      }

      /**
       * @test
       */
      public function onlyCoordinatorCanSaveOperatorAccounts()
      {
          $account = factory(Account::class)->make(['is_operator_account' => true]);
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

      /**
       * @test
       */
      public function anAccountBalanceDecreaseWhenATransactionIsAssigned()
      {
          $this->withoutEvents();
          $venezuelanuser = $this->actingAsVenezuelanOperator();
          $account = factory(Account::class)->create(['user_id' => $venezuelanuser->id]);
          $account->refresh();
          $this->actingAsCoordinator();
          $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $account->id])
        ->assertStatus(201);

          $venezuelanuser->refresh();

          $accountInitialBalance = $account->balance;
          factory(Transaction::class)->create(['to_account_id' => $account, 'amount' => '1000000']);
          $account->refresh();
          $accountFinalBalance = $account->balance;
          dump($accountInitialBalance - $accountFinalBalance);
      }
  }
