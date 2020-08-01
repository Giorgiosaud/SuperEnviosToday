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
    public function testBank()
    {
        $account=factory(Account::class)->create();
        $this->assertEquals($account->bank_id,$account->bank->id);

    }
    /**
     *
     */
    public function testTransactions()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,10)->create(['account_id'=>$account->id]);
        factory(Transaction::class,10)->create(['account_id'=>$account->id]);
        $account->refresh();
        $this->assertCount(20,$account->allTransactions);


    }

    public function testGetBalanceAttribute()
    {
        $account=factory(Account::class)->create();
        factory(Transaction::class,50)->create(['account_id'=>$account->id,'amount'=>-100000]);
        factory(Transaction::class,50)->create(['account_id'=>$account->id,'amount'=>50000]);
        $account->refresh();
        $this->assertEquals((50000-100000)*50,$account->balance);

    }

}
