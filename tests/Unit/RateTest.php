<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\Rate;
use Tests\TestCase;

class RateTest extends TestCase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAmountAttribute(){
        $rate=Rate::factory(['amount'=>'100'])->create();
        $this->assertEquals(100,$rate->amount);
    }
    public function testCcurrency(){
        $rate=Rate::factory(['amount'=>'100'])->create();
        $this->assertInstanceOf(Currency::class,$rate->currency);

    }

}
