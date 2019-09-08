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
        $operatorAccount = factory(Account::class)->create(['user_id' => $operatorVenezuela->id]);
        $operatorForgeinAccount = factory(Account::class)->create(['user_id' => $operatorForeign->id]);
        factory(Rate::class)->create(['currency_id' => $operatorForgeinAccount->bank->currency->id, 'since' => Carbon::yesterday()]);
        $related = factory('App\User')->create();
        $relatedAccount = factory(Account::class)->create(['user_id' => $related->id]);
        $transaction = [
        'client_id'                      => $client->id,
        'foreign_account_id'             => $operatorForgeinAccount->id,
        'foreign_attachment_id'          => $foreignAttachment->id,
        'receiver_account_id'            => $relatedAccount->id,
        'venezuelan_operator_account_id' => $operatorAccount->id,
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
        $this->actingAsCoordinator();
        $pt = factory(PendingTransaction::class)->create(['status' => 'pending']);
        $this->patchJson("api/approve-transaction/$pt->id");
        $pt->refresh();
        $this->assertEquals('aprooved', $pt->status);
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
