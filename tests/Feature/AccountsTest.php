<?php

namespace Tests\Feature;

use App\Account;
use App\Bank;
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
      $user=factory(User::class)->create();
      $bank=factory(Bank::class)->create();
      $account = factory(Account::class)->make();
      $userAccount=[
        'user_id' => $user->id,
        'bank_id' => $bank->id,
        'type'    => $account->type,
        'number'    => $account->number,
      ];
      $this->postJson(route('save_account'), $userAccount)->assertStatus(401);
      $this->actingAsReceiver();
      $this->postJson(route('save_account'), $userAccount)->assertStatus(403);
      $this->actingAsClient();
      $this->postJson(route('save_account'), $userAccount)->assertStatus(403);
      $this->actingAsVenezuelanOperator();
      $this->postJson(route('save_account'), $userAccount)->assertStatus(403);
      $this->actingAsForeignOperator();
      $this->postJson(route('save_account'), $userAccount)->assertStatus(201);
      $userAccount['number']=$userAccount['number'].'1';
      $coord=$this->actingAsCoordinator();
      $this->postJson(route('save_account'), $userAccount)->assertStatus(201);
    }
    
    /**
    * @test
    */
    public function onlyCoordinatorCanSaveOperatorAccounts()
    {
      $account = factory(Account::class)->make();
      $user=factory(User::class)->create();
      $account['user_id']=$user->id;
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
      $venezuelanUser = $this->actingAsVenezuelanOperator();
      $account = factory(Account::class)->create();
      $account->owners()->sync([$venezuelanUser->id], false);
      $account->refresh();
      $this->actingAsCoordinator();
      $this->postJson(route('add_money_to_venezuela'), [
        'amount' => '10000000000', 
        'to_user_id'=>$venezuelanUser->id,
        'to_account_id' => $account->id
        ])
      ->assertStatus(201);
      $venezuelanUser->refresh();
      $accountInitialBalance = $account->balance;
      $client=factory(User::class)->create();
      $clientAccount = factory(Account::class)->create(['bank_id'=>$account->bank_id,'is_operator_account'=>false]);
      $clientAccount->owners()->sync([$client->id], false);
      factory(Transaction::class)->create([
        'client_id'=>$client->id,
        'from_account_id' => $account->id,
        'to_account_id'=>$clientAccount->id,
        //'status'=>'assigned',
        'from_user_id'=>$venezuelanUser->id,
        'to_user_id'=>$client->id,
        'type' => 'outcome',
        'amount' => '1000000'
        ]);
        
        $account->refresh();
        $accountFinalBalance = $account->balance;
      }
    }
    