<?php

namespace App\Http\Controllers;

use App\Currency;
use App\Rate;
use Illuminate\View\View;


class RateController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $rates = Rate::with(array('currency'=>function($query){
            $query->select('name','id','sign');
        }))->paginate();
        $currencies = Currency::all();
        return view('coordinator.rates.index', compact('rates','currencies'));
    }
}
