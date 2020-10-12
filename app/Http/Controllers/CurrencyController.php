<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $currencies = Currency::paginate();
        return view('coordinator.currencies.index', compact('currencies'));
    }
}
