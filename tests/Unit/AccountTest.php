<?php

namespace Tests;

use App\Account;
use App\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     *
     */
    public function testIncomingTransactions()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,10)->create(['to_account_id'=>$account->id]);
        $this->assertCount(10,$account->incomingTransactions);

    }

    /**
     *
     */
    public function testBank()
    {
        $account=factory(Account::class)->create();
        $this->assertEquals($account->bank_id,$account->bank->id);

    }

    /**
     *
     */
    public function testOutgoingTransactions()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,10)->create(['from_account_id'=>$account->id]);
        $this->assertCount(10,$account->outgoingTransactions);


    }

    /**
     *
     */
    public function testTransactions()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,10)->create(['from_account_id'=>$account->id]);
        factory(Transaction::class,10)->create(['to_account_id'=>$account->id]);
        $account->refresh();
        $this->assertCount(20,$account->transactions);


    }

    /**
     *
    public function testIncomingTransactionsTyped()
    {

    }

    public function testOwners()
    {

    }
     */

    public function testGetBalanceAttribute()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,50)->create(['from_account_id'=>$account->id,'amount'=>100000,'type'=>'outcome']);
        factory(Transaction::class,50)->create(['to_account_id'=>$account->id,'amount'=>50000,'type'=>'income']);
        $account->refresh();
        $this->assertEquals((50000-100000)*50,$account->balance);

    }
    /**
    public function testOutgoingTransactionsTyped()
    {

    }

    public function testBalanceCache()
    {


    }
    */
}
