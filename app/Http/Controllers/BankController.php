<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Bank[]|Collection
     */
    public function index()
    {
        return Bank::all();
        //
    }

    public function country_index(Currency $currency)
    {
        return $currency->banks;
    }

    public function venezuelan_index()
    {
        $currency = Currency::whereName('Bolivares Soberanos')->first();

        return $currency->banks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Bank
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
                'name'        => 'required',
                'currency_id' => 'required',
            ]);
        $bank = Bank::create($validated);

        return $bank;
        //
    }
}
