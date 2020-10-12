<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Attachment;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class TransactionTest extends TestCase
{
  /**
   *
   */
  public function setUp(): void
  {
    parent::setUp();
  }

  public function testTransactionAttributes()
  {
    $transaction = Transaction::factory(['amount' => 100])->create();
    $this->assertInstanceOf(Account::class, $transaction->account);
    $this->assertEquals(100, $transaction->amount);
  }

  public function testAttachments()
  {
    $attachment = Attachment::factory()->create();
    $transaction = Transaction::factory()->create();
    $transaction->attachments()->save($attachment);
    $this->assertEquals(1, $transaction->fresh()->attachments->count());

  }

  public function testRelatedTransactions()
  {
    $transactions = Transaction::factory(['track_number' => 'asd123'])->count(10)->create();
    $this->assertCount(10, $transactions[0]->related);
  }


}
