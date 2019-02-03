<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * a transactions must be made to an Account
     * @test
     */
    public function aTransactionMustHaveADestinationAccount()
    {
        $transaction = factory('App\Transaction')->create();
        $this->assertInstanceOf(\App\Account::class, $transaction->destinationAccount);
    }
    /**
     * a transactions should be from an Account
     * @test
     */
    public function aTransactionShouldHaveAOriginAccount()
    {
        $transaction = factory('App\Transaction')->create();
        $this->assertTrue(
            $transaction->originAccount instanceof \App\Account
                || $transaction->originAccount === null
        );
    }
}
