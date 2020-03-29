<?php

namespace Tests\Feature;

use App\Account;
use App\Attachment;
use App\PendingTransaction;
use App\Rate;
use App\Setting;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
* Class UserTest.
*
* @property Account $venezuelan_account
*/
class TransactionsTest extends TestCase
{
  use RefreshDatabase;
  protected $venezuelan_operator;
  protected $venezuelan_account;
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aCoordinatorCanAddFundsToVenezuelanOperator()
  {
    $this->withoutEvents();
    $this->addVenezuelanOperatorAndAccount();
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '1000000', 'to_user_id'=>$this->venezuelan_operator->id,'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(201);
    $this->venezuelan_account->refresh();
    $this->assertEquals(1000000, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aForgeinOperatorCantAddFundsToVenezuelanOperator()
  {
    $this->actingAsForeignOperator();
    $this->addVenezuelanOperatorAndAccount();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(403);
    $this->venezuelan_account->refresh();
    $this->assertEquals(0, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aVenezuelanOperatorCantAddFundsToVenezuelanOperator()
  {
    $this->actingAsVenezuelanOperator();
    $this->addVenezuelanOperatorAndAccount();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(403);
    $this->venezuelan_account->refresh();
    $this->assertEquals(0, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aClientOperatorCantAddFundsToVenezuelanOperator()
  {
    $this->actingAsClient();
    $this->addVenezuelanOperatorAndAccount();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(403);
    $this->venezuelan_account->refresh();
    $this->assertEquals(0, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aReceiverOperatorCantAddFundsToVenezuelanOperator()
  {
    $this->actingAsReceiver();
    $this->addVenezuelanOperatorAndAccount();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(403);
    $this->venezuelan_account->refresh();
    $this->assertEquals(0, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aGuestOperatorCantAddFundsToVenezuelanOperator()
  {
    $this->addVenezuelanOperatorAndAccount();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $this->venezuelan_account->id])
    ->assertStatus(401);
    $this->venezuelan_account->refresh();
    $this->assertEquals(0, $this->venezuelan_account->balance);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function onlyACoordinatorOrForeignOperatorCanMakeATransaction(): void
  {
    $this->withoutEvents();
    factory(Setting::class)->create(['key' => 'venezuelanBankTax', 'value' => '2']);
    $transaction = $this->defineTransactionData();
    $this->actingAsGuest();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(401);
    $this->actingAsReceiver();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(403);
    $this->actingAsClient();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(403);
    $this->actingAsVenezuelanOperator();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(403);
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $transaction['venezuelan_operator_account_id']]);
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
    $this->actingAsForeignOperator();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
  }
  
  /**
  * @test
  */
  public function aTransactionCanHaveManyTtransactionAttachments()
  {
    $this->withoutEvents();
    $transaction = $this->defineTransactionData();
    $attachment = factory(Attachment::class)->create();
    $transaction['received_transaction_attachment_ids'] = [$attachment->id];
    //    $transaction['client_id']=factory(User::class)->create()->id;
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $transaction['venezuelan_operator_account_id']]);
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
    $transaction = Transaction::whereClientId($transaction['client_id'])->first();
    $this->assertEquals($attachment->id, $transaction->attachments->first()->id);
  }
  
  /**
  * @test
  */
  public function whenAnAllowedActorMakeANormalTransactionTwoTransactionsAreGenerated(): void
  {
    $this->withoutEvents();
    $transaction = $this->defineTransactionData();
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '1000000', 'to_account_id' => $transaction['venezuelan_operator_account_id']]);
    
    $this->actingAsForeignOperator();
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
    $transactions = Transaction::all();
    $this->assertCount(4, $transactions);
    $this->assertEquals('confirmed', $transactions[1]->status);
    $this->assertEquals('assigned', $transactions[2]->status);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function aCoordinatorCanMakeATransactionWithCustomExchangeRate(): void
  {
    $this->withoutEvents();
    $transaction = $this->defineTransactionData(false);
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '100000000000', 'to_account_id' => $transaction['venezuelan_operator_account_id']]);
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
    $transactions = Transaction::all();
    $this->assertCount(4, $transactions);
    $this->assertEquals('confirmed', $transactions[1]->status);
    $this->assertEquals('assigned', $transactions[2]->status);
    $this->assertEquals(2000, $transactions[2]->amount);
  }
  
  /**
  * A basic test example.
  *
  * @test
  *
  * @return void
  */
  public function ifAForeignOperatorCanMAbeATransactionWithCustomExchangeRateButItWillBeCreatedAsPendingUntilACoordinatorApprove()
  {
    $this->withoutEvents();
    $transaction = $this->defineTransactionData(false);
    $this->actingAsForeignOperator();
    $this->postJson(route('add_money_to_venezuela'), ['amount' => '10000000000', 'to_account_id' => $transaction['venezuelan_operator_account_id']]);
    $this->postJson(route('save_transaction'), $transaction)
    ->assertStatus(201);
    $this->assertCount(1, PendingTransaction::all());
  }
  /**
   * @test
   *
   * @return void
   */
  public function anAdjustmentTransactionofTypeIncomeWillIncrementBalanceInAccount(){
    $this->addVenezuelanOperatorAndAccount();
    $this->actingAsCoordinator();
    $this->postJson(route('adjust-transaction'),
    [
      'to_account_id' => $this->venezuelan_account->id,
      'to_user_id' =>$this->venezuelan_operator->id,
      'type'          => 'income',
      'amount'        => '100',
      ]
    )->assertStatus(201);
    $this->venezuelan_account->refresh();
    $this->assertEquals(100,$this->venezuelan_account->balance);
    
  }
  /**
   * @test
   *
   * @return void
   */
  public function anAdjustmentTransactionofTypeOutcomeWillDecrementBalanceInAccount(){
    $this->addVenezuelanOperatorAndAccount();
    $this->actingAsCoordinator();
    $this->postJson(route('adjust-transaction'),
    [
      'to_account_id' => $this->venezuelan_account->id,
      'to_user_id' =>$this->venezuelan_operator->id,
      'type'          => 'income',
      'amount'        => '100',
      ]
    )->assertStatus(201);;
    $this->postJson(route('adjust-transaction'),
    [
      'to_account_id' => $this->venezuelan_account->id,
      'to_user_id' =>$this->venezuelan_operator->id,
      'type'          => 'outcome',
      'amount'        => '50',
      ]
    )->assertStatus(201);;
    $this->venezuelan_account->refresh();
    $this->assertEquals(50,$this->venezuelan_account->balance);
    
  }
  /**
  * @param bool $normal
  *
  * @return array
  */
  protected function defineTransactionData($normal = true): array
  {
    $client = factory(User::class)->create();
    $operatorForeign = factory(User::class)->create();
    $operatorForeign->setRole('foreign_operator');
    $operatorForeign->refresh();
    $foreignAttachment = factory(Attachment::class)->create();
    $this->addVenezuelanOperatorAndAccount();
    $this->actingAsCoordinator();
    $this->postJson(route('add_money_to_venezuela'), ['to_user_id'=>$this->venezuelan_operator->id,'amount' => '1000000', 'to_account_id' => $this->venezuelan_account->id])->assertStatus(201);
    $this->venezuelan_account->refresh();
    $this->assertEquals(1000000, $this->venezuelan_account->balance);
    $operatorForgeinAccount = factory(Account::class)->create();
    $operatorForgeinAccount->owners()->sync([$operatorForeign->id], false);
    factory(Rate::class)->create(['currency_id' => $operatorForgeinAccount->bank->currency->id, 'since' => Carbon::yesterday()]);
    $related = factory('App\User')->create();
    $relatedAccount = factory(Account::class)->create();
    $relatedAccount->owners()->sync([$related->id], false);
    $transaction = [
      'client_id'                      => $client->id,
      'foreign_account_id'             => $operatorForgeinAccount->id,
      'foreign_attachment_id'          => $foreignAttachment->id,
      'receiver_account_id'            => $relatedAccount->id,
      'venezuelan_operator_account_id' => $this->venezuelan_account->id,
      'venezuelan_operator_id' => $this->venezuelan_operator->id,
      'transaction_number'=>'123123',
      'receiver_user_id'=>$related->id,
      'amount'                         => 1000,
    ];
    if (!$normal) {
      $transaction['rate'] = 2;
    }
    
    return $transaction;
  }
}
