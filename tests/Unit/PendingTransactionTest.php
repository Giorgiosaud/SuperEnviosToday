<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Attachment;
use App\Models\PendingTransaction;
use App\Models\User;
use Tests\TestCase;

class PendingTransactionTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAttachedImage(){
        $attachment=Attachment::factory()->create();
        $pendingTransaction=PendingTransaction::factory()->create();
        $pendingTransaction->attachments()->save($attachment);
        $this->assertCount(1,$pendingTransaction->fresh()->attachments);
    }
    public function testClient(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(User::class,$pendingTransaction->client);
    }
    public function testReceiver(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(User::class,$pendingTransaction->receiver);
    }
    public function testVenezuelanOperator(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(User::class,$pendingTransaction->venezuelanOperator);
    }
    public function testForeignOperator(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(User::class,$pendingTransaction->foreignOperator);
    }
    public function testForeignAccount(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(Account::class,$pendingTransaction->foreignAccount);
    }
    public function testReceiverAccount(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(Account::class,$pendingTransaction->receiverAccount);
    }
    public function testLocalOperatorAccount(){
        $pendingTransaction=PendingTransaction::factory()->create();
        $this->assertInstanceOf(Account::class,$pendingTransaction->localOperatorAccount);
    }
    public function testGetRateAttribute(){
        $pendingTransaction=PendingTransaction::factory(['rate'=>100])->create();
        $this->assertEquals(100,$pendingTransaction->rate);
    }
    public function testGetAmountAttribute(){
        $pendingTransaction=PendingTransaction::factory(['amount'=>100])->create();
        $this->assertEquals(100,$pendingTransaction->amount);
    }


}
