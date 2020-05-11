<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(){
        return Currency::select('id','name','identifier')->all();
    }
    public function foreign(){

        return Currency::where('id','!=',config('app.base_currency_id'))->get();
    }
    //
}
