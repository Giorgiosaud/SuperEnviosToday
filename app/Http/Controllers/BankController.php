<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Currency;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $banks = Bank::with('currency')->paginate();
        $currencies = Currency::all();
        return view('coordinator.banks.index', compact('banks','currencies'));
    }
}
