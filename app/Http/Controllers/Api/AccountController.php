<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Currency;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;


class AccountController extends Controller
{
    public function getAccounts($currencyId)
    {
        $banks = Bank::select('id')->where('currency_id', $currencyId)->pluck('id');
        return Account::with('bank')
            ->whereIn('bank_id', $banks)
            ->whereHas('owners', function ($q) {
                return $q->where('user_id', auth()->user()->id);
            })
            ->where('is_operator', true)
            ->get();
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function link(User $user, Request $request)
    {
        $data=$request->validate([
            'bank_id'=>['required'],
            'number'=>['required','numeric'],
            'type'=>[]
        ]);
        $account=Account::where('bank_id',$data['bank_id'])
            ->where('number',$data['number'])
            ->where('type',$data['type'])
            ->withTrashed()
            ->first();
        if(!$account){
            $account = Account::create($data);
        }else{
            $account->restore();
        }
        return $user->accounts()->save($account);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function indexBase(){
        $banks = Bank::select('id')->where('currency_id', config('app.base_currency_id'))->pluck('id');
        return Account::with(['bank','owners'])
            ->whereIn('bank_id', $banks)
            ->where('is_operator', true)
            ->get()
            ->append('balance');
    }

    public function update(Account $account,Request $request){
        $data=$request->validate([
            'bank_id'=>['required'],
            'number'=>['required','numeric'],
            'type'=>[]
        ]);
        $account->update($data);
        return $account;
    }

    /**
     * @param Account $account
     * @param User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function unlink(Account $account, User $user){
        $account->owners()->detach($user->id);
        if($account->owners->count()==0){
            $account->delete();
        }
        return response('Unlinked Account', 204);
    }
    //
}
