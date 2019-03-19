<?php

namespace App\Listeners;

use App\Events\RegisteredOperator;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddCashAccount
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RegisteredOperator  $event
     * @return void
     */
    public function handle(RegisteredOperator $event)
    {
        //
    }
}
