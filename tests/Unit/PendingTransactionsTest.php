<?php

namespace Tests\Unit;

use App\Account;
use App\Attachment;
use App\PendingTransaction;
use App\Rate;
use App\User;
use App\Transaction;
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
    public function aTransactionWithDifferentRateThanActualShouldBeCreatedAsPendingTransaction()
    {
        $this->rateSeed();
        $rate=Rate::first();
        $transaction=[
            'client_id'                             => factory(User::class)->create()->id,
            'foreign_account_id'                    => factory(Account::class)->create()->id,
            'received_transaction_attachment_ids.*' => factory(Attachment::class)->create()->id,
            'receiver_account_id'                   => factory(Account::class)->create()->id,
            'venezuelan_operator_account_id'        => factory(Account::class)->create()->id,
            'venezuelan_operator_id'                => factory(User::class)->create()->id,
            'transaction_number'                    => factory(Transaction::class)->create()->id,
            'receiver_user_id'                      => factory(User::class)->create()->id,
            'rate'                                  => '100000000000',
            'amount'                                => '100000000000',
        ];
        $this->actingAsForeignOperator();
        $pendingTransaction=$this->postJson(route('save_transaction'), $transaction);
        $this->assertCount(1, PendingTransaction::all());
    }
}
