<?php

namespace Tests\Feature;

use App\Account;
use App\Attachment;
use App\PendingTransaction;
use App\Rate;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendingTransactionsTest extends TestCase
{
    use RefreshDatabase;
    
    /**
    * A basic feature test example.
    *
    * @test void
    */
    public function aPendingTransactionIsCreatedOnDifferentRateByDefaultAssignedByForeign()
    {
        $this->withoutEvents();
        $this->actingAsForeignOperator();
        $transaction = $this->defineTransactionData(false);
        $this->postJson(route('save_transaction'), $transaction)
        ->assertStatus(201);
        $pts = PendingTransaction::all();
        $this->assertCount(1, $pts);
        $this->assertEquals('pending', $pts->first()->status);
    }
    
    protected function defineTransactionData($normal = true): array
    {
        $client = factory(User::class)->create();
        $operatorForeign = factory(User::class)->create();
        $operatorForeign->setRole('foreign_operator');
        $operatorForeign->refresh();
        $foreignAttachment = factory(Attachment::class)->create();
        $operatorVenezuela = factory(User::class)->create();
        $operatorVenezuela->setRole('venezuelan_operator');
        $operatorVenezuela->refresh();
        $operatorAccount = factory(Account::class)->create();
        $operatorAccount->owners()->sync(['user_id'=>$operatorVenezuela->id], false);
        $operatorForgeinAccount = factory(Account::class)->create();
        $operatorForgeinAccount->owners()->sync(['user_id'=>$operatorForeign->id], false);
        factory(Rate::class)->create(['currency_id' => $operatorForgeinAccount->bank->currency->id, 'since' => Carbon::yesterday()]);
        $related = factory('App\User')->create();
        $relatedAccount = factory(Account::class)->create();
        $relatedAccount->owners()->sync(['user_id'=>$related->id], false);
        $transaction = [
            'client_id'                      => $client->id,
            'foreign_account_id'             => $operatorForgeinAccount->id,
            'foreign_attachment_id'          => $foreignAttachment->id,
            'receiver_user_id'            => $related->id,
            'receiver_account_id'            => $relatedAccount->id,
            'venezuelan_operator_id' => $operatorVenezuela->id,
            'venezuelan_operator_account_id' => $operatorAccount->id,
            'transaction_number'=>'213123',
            'amount'                         => 10000,
        ];
        if (!$normal) {
            $transaction['rate'] = 2;
        }
        
        return $transaction;
    }
    
    /**
    * @test
    */
    public function onlyACoordinatorCanApproveAPendingTransaction()
    {
        $this->withoutEvents();
          
          $this->addVenezuelanOperatorAndAccount();
          $this->actingAsCoordinator();
          
          $this->postJson(route('add_money_to_venezuela'), ['to_user_id'=>$this->venezuelan_operator->id,'amount' => '1000000', 'to_account_id' => $this->venezuelan_account->id])->assertStatus(201);
          $this->venezuelan_account->refresh();
          $this->assertEquals(1000000, $this->venezuelan_account->balance);
        $this->actingAsForeignOperator();
        $receiver=factory(User::class)->create();
        $receiverAccount=factory(Account::class)->create(['bank_id'=>$this->venezuelan_account->bank_id]);
        $receiverAccount->owners()->sync([$receiver->id], false);
        
        
        $pendingTransaction = factory(PendingTransaction::class)->create(['status' => 'pending','amount'=>'100000','receiver_account_id'=>$receiverAccount->id,'venezuelan_operator_account_id'=>$this->venezuelan_account->id]);
        
        $this->actingAsCoordinator();
        $this->patchJson(route('approve-transaction',$pendingTransaction->id))->assertStatus(200);
        $pendingTransaction->refresh();
        $this->assertEquals('aprooved', $pendingTransaction->status);
    }
    /**
    * @test
    */
    public function aPendingTransactionWithRefereneVenezuelanAccountOutOfFundsCantBeApproved()
    {
        $this->actingAsForeignOperator();
        $pendingTransaction = factory(PendingTransaction::class)->create(['status' => 'pending']);
        $this->actingAsCoordinator();
        $this->patchJson(route('approve-transaction',$pendingTransaction->id))
        ->assertStatus(424)
        ->assertJson(["message"=> "No hay dinero disponible suficiente en la cuenta seleccionada"]);
        $pendingTransaction->refresh();
        $this->assertEquals('pending', $pendingTransaction->status);
    }
    /**
    * @test
    */
    public function onlyACoordinatorCanCancelAPendingTransaction()
    {
        $this->actingAsCoordinator();
        $pt = factory(PendingTransaction::class)->create(['status' => 'pending']);
        $this->patchJson("api/reject-transaction/$pt->id");
        $pt->refresh();
        $this->assertEquals('rejected', $pt->status);
    }
}
