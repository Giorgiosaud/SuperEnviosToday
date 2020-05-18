<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function baseBanks()
    {
        $currency=Currency::find(config('app.base_currency_id'));
        return $currency->banks;


    }
}
