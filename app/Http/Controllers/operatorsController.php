<?php

namespace App\Http\Controllers;

use App\Role;

class operatorsController extends Controller
{
    public function index()
    {
        return view('coordinator.addFundsToOperator');
    }

    public function venezuelanList()
    {
        return view('operators.venezuelan-list');
    }

    public function foreignList()
    {
        return view('operators.foreign-list');
    }

    public function venezuelanIndex()
    {
        $users= Role::whereName('Operador Venezolano')
                ->first()
                ->users()
                ->with('accounts')
                ->get();
                foreach ($users as $user) {
                  foreach($user->accounts as $account){
                    $account['Balance']=$account->getBalanceAttribute();
                  }
                }
        return $users;
    }

    public function foreignIndex()
    {
        $users= Role::whereName('Operador Extranjero')
                ->first()
                ->users()
                ->with('accounts')
                ->get();
                foreach ($users as $user) {
                  foreach($user->accounts as $account){
                    $account['Balance']=$account->getBalanceAttribute();
                  }
                }
                return $users;
    }

    //
}
