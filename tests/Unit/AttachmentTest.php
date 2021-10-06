<?php

namespace Tests\Unit;

use App\Models\Attachment;
use App\Models\Transaction;
use Tests\TestCase;

class AttachmentTest extends TestCase
{

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAttachableImage(){
        $attachment=Attachment::factory()->create();
        $transaction=Transaction::factory()->create();
        $transaction->attachments()->save($attachment);
        $this->assertEquals(1,$attachment->fresh()->transactions->count());
    }


}
