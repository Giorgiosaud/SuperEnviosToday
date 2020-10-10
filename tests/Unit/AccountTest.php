<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class AccountTest extends TestCase
{

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
        $account=Account::factory()->create();
        $this->assertEquals($account->bank_id,$account->bank->id);

    }
    /**
     *
     */
    public function testTransactions()
    {
        $account=Account::factory()->create();
        Transaction::factory(10)->create(['account_id'=>$account->id]);
        Transaction::factory(10)->create(['account_id'=>$account->id]);
        $account->refresh();
        $this->assertCount(20,$account->transactions);
    }

    public function testGetBalanceAttribute()
    {
        $account=Account::factory()->create();
        Transaction::factory(50)->create(['account_id'=>$account->id,'amount'=>-100000]);
        Transaction::factory(50)->create(['account_id'=>$account->id,'amount'=>50000]);
        $account->refresh();
        $this->assertEquals((50000-100000)*50,$account->balance);

    }
    public function testOwnersOfAccount(){
        $account=Account::factory()->create();
        $user=User::factory()->create();
        $account->owners()->save($user);
        $this->assertCount(1,$account->fresh()->owners);
    }
    public function testNewAccountBalanceZero(){
        $account=Account::factory()->create();
        $this->assertEquals(0,$account->fresh()->balance);
    }

}
