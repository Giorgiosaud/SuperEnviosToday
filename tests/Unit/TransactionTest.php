<?php

namespace Tests\Unit;

use App\Account;
use App\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * a transactions must be made to an Account.
     *
     * @test
     */
    public function aTransactionADestinationAccountCantBeNull()
    {
        $this->expectException(QueryException::class);
        factory('App\Transaction')->create(['to_account_id'=>null]);
    }

    /**
     * a transactions should be from an Account.
     *
     * @test
     */
    public function aTransactionShouldHaveAOriginAccount()
    {
        $transaction = factory(Transaction::class)->create(['from_account_id'=>factory(Account::class)->create()->id]);
        $this->assertInstanceOf(Transaction::class,$transaction);
        $transaction = factory(Transaction::class)->create(['from_account_id'=>null]);
        $this->assertInstanceOf(Transaction::class,$transaction);
    }
}
