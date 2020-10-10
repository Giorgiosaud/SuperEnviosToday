<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Bank;
use Tests\TestCase;

class BankTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCurrency(){
        $bank=Bank::factory()->chilean()->create();

        $this->assertEquals('CLP',$bank->currency->identifier);
    }
    public function testAccounts(){
        $account=Account::factory()->create();
        $bank=$account->bank;
        $this->assertCount(1,$bank->fresh()->accounts);
    }

}
