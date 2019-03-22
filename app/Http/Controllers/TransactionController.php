<?php

namespace App\Http\Controllers;

use App\Rate;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('operators.transactions');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        return view('operators.transactions-pending');

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validData=$request->validate([
            'to_account_id'=>'required|numeric',
            'amount'=>'required|numeric'
        ]);
        $validData['emitter_operator']=$request->user()->id;
        $validData['status']='terminated';
        $validData['type']='income';
        return Transaction::create($validData);
        //
    }

    /**
     * @param Request $request
     * @return RateController
     */
    public function normalstore(Request $request){
    $validData=$request->validate([
        'to_account_id'=>'required|numeric',
        'from_account_id'=>'required|numeric',
        'from_client_id'=>'required|numeric',//TODO ADD LIMIT TO ACCOUNTS IN DB
        'amount'=>'required|numeric',
        'foreign_currency_id'=>'required|numeric'
    ]);
    $rate= Rate::whereCurrencyId($validData['foreign_currency_id'])->orderBy('since', 'DESC')->first();
    $validData['amount']=$rate['amount']*$validData['amount'];
    $validData['emitter_operator']=$request->user()->id;
    $validData['status']='assigned';
    return Transaction::create($validData);
}
    /**
     * Display the specified resource.
     *
     * @param \App\Transaction $transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Transaction $transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Transaction         $transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Transaction $transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
