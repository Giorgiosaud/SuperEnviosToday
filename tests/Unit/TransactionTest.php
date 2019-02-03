<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * a transactions must belong to an Account
     * @test
     */
    public function aTransactionMustBelongToAnAccount()
    {
        $transaction = factory('App\Transaction')->create();
        $this->assertInstanceOf(\App\Account::class, $transaction->account);

    }
}
