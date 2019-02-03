<?php

namespace Tests\Unit;

use Tests\TestCase;
use \App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A Bank can have accounts Asociated
     * @test
     */
    public function aBankCanHaveAccountsAssociated()
    {
        $bank = factory(\App\Bank::class)->create();
        $accounts = factory(\App\Account::class, 10)->create(['bank_id' => $bank->id]);
        $this->assertCount(10, $bank->accounts);
    }
}
