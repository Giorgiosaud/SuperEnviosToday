<?php

namespace Tests\Unit;

use App\PendingTransaction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PendingTransactionsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @test void
     */
    public function aPendingTransactionJustCreatedShouldBeOfStatusPending()
    {
        $pendingTransaction=factory(PendingTransaction::class)->create();
        $this->assertInstanceOf(PendingTransaction::class,$pendingTransaction);
    }
}
