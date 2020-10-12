<?php

namespace Tests\Unit;

use App\Models\Bank;
use App\Models\Currency;
use Tests\TestCase;

class CurrencyTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCurrencyCanAccessToBanks(){
        Bank::factory()->count(3)->chilean()->create();
        $currency=Currency::where('identifier','CLP')->first();
        $this->assertCount(3,$currency->banks);

    }

}
