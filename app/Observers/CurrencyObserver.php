<?php

namespace App\Observers;

use App\Bank;
use App\Currency;

class CurrencyObserver
{
    public function created(Currency $currency)
    {
        factory(Bank::class)->create([
            'name'=>'Cash',
            'currency_id'=>$currency->id
        ]);
        //
    }
    //
}
