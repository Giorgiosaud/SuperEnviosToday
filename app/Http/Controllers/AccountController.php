<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Currency;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function index()
    {
        return Account::all();
    }

    public function operatorsIndex()
    {
        return Account::where('is_operator_account', 1)->get();
    }

    /**
     * @param Request $request
     * @return Account|\Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request)
    {
        $validInputs = $request->validate([
            'user_id' => 'required|numeric',
            'bank_id' => 'required|numeric',
            'type' => 'in:corriente,ahorro',
            'number' => 'required|string',
        ]);
        $account = Account::firstOrCreate($request->only(['bank_id', 'number', 'type']));
        $account->owners()->sync([$validInputs['user_id']], false);

        return $account;
    }

    /**
     * @param Request $request
     * @return Account|\Illuminate\Database\Eloquent\Model
     */
    public function storeOperatorAccount(Request $request)
    {
        $validInputs = $request->validate([
            'user_id' => 'required|numeric',
            'bank_id' => 'required|numeric',
            'number' => 'required|string',
            'type' => 'string',
        ]);
        $validInputs['is_operator_account'] = true;
        $account = Account::firstOrCreate($request->only(['bank_id', 'number', 'type']));
        $account->owners()->sync([$validInputs['user_id']], false);

        return $account;
        //
    }

    public function foreignAccounts()
    {
        $currencies = Currency::where('identificator', '<>', 'BsS')->get()->pluck('id');
        $banks = Bank::whereIn('currency_id', $currencies)->get()->pluck('id');
        $accounts = Account::whereIn('bank_id', $banks)->get();

        return $accounts;
    }
    public function foreignAccountsNew()
    {
        $currencies = Currency::where('identificator', '<>', 'BsS')->get()->pluck('id');
        $banks = Bank::whereIn('currency_id', $currencies)->get()->pluck('id');
        $accounts = Account::whereIn('bank_id', $banks)->with('owners')->where('is_operator_account', true)->get();
        foreach ($accounts as $account) {
            $account['Balance'] = $account->getBalanceAttribute();
        }
        return $accounts;
    }

    public function asociateForeignAccounts(Request $request)
    {
        $validInputs = $request->validate([
            'user_id' => 'required|numeric',
            'account_id' => 'required|numeric',
        ]);
        $account = Account::find($validInputs['account_id']);
        $account->owners()->sync([$validInputs['user_id']], false);

        return $account;
    }

    public function addAccounts(Request $request)
    {
        $validInputs = $request->validate([
            'bank_id' => 'required|numeric',
            'number' => 'required|numeric',
            'type' => 'nullable|in:ahorro,corriente',
        ]);
        $validInputs['is_operator_account'] = true;

        return Account::create($validInputs);
    }

    public function removeAccount(Account $account, User $user)
    {
        return $account->owners()->detach($user->id);
    }
    public function removeAccountAsOperator(Account $account){
        $account->is_operator_account=false;
        $account->save();
    }
    public function venezuelanAccounts()
    {
        return view('accounts.venezuelan-list');
    }
     public function foreignList()
    {
        return view('accounts.foreign-list');
    }

    public function venezuelanIndex()
    {
        $currency = Currency::where('identificator', 'BsS')->first();
        $banks = Bank::where('currency_id', $currency->id)->get()->pluck('id');
        $accounts = Account::whereIn('bank_id', $banks)->with('owners')->where('is_operator_account', true)->get();
        foreach ($accounts as $account) {
            $account['Balance'] = $account->getBalanceAttribute();
        }
        return $accounts;
    }
}
