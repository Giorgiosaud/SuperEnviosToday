<?php

namespace Tests\Unit;

use App\PendingTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $pendingTransaction = factory(PendingTransaction::class)->create();
        $this->assertInstanceOf(PendingTransaction::class, $pendingTransaction);
    }
}
