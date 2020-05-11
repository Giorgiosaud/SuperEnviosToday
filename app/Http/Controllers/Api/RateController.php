<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function get($currencyId)
    {
        return Rate::whereCurrencyId($currencyId)->orderBy('since', 'DESC')->first();
    }
    //
}
