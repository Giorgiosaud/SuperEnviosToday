<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $banks = Bank::with('currency')->paginate();
        $currencies = Currency::all();
        return view('coordinator.banks.index', compact('banks','currencies'));
    }
}
