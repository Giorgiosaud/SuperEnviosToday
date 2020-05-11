<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAccounts($currencyId){
    $banks= Bank::select('id')->where('currency_id',$currencyId)->pluck('id');
    $accounts=Account::with('bank')
        ->whereIn('bank_id',$banks)
        ->whereHas('owners',function($q){
            return $q->where('user_id',auth()->user()->id);
        })
        ->where('is_operator',true)
        ->get();
    return $accounts;
    /*


        ->select('id','type','number')
        ->get();
    */
    }
    //
}
