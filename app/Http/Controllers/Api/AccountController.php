<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatedAccount;
use App\Events\RestoredAccount;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class AccountController extends Controller
{
  public function index()
  {

    $request = request();
    $currencies = $request->currencies;
    $currencies = $currencies ? explode(',', $currencies) : Currency::all()->pluck('id');
    $banks = $currencies ? Bank::whereIn('currency_id', $currencies)->get() : Bank::all();
    $banksID = $banks->pluck('id');
    $accounts = Account::whereIsOperator(true)
      ->whereIn('bank_id', $banksID)
      ->with('bank.currency')
      ->with('owners');
    if ($request->has('perPage')) {
      $accounts = $accounts->paginate($request->perPage);
    } else {
      $accounts = $accounts->paginate();
    }
    $accounts->append(['balance']);

    return $accounts;
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'currency' => ['required', 'numeric', 'exists:currencies,id'],
      'bank' => ['required', 'numeric', 'exists:banks,id'],
      'number' => ['required', 'numeric'],
      'type' => ['required'],
    ]);
    $data['bank_id'] = $data['bank'];
    $data['is_operator'] = true;
    $account = Account::where('number', $data['number'])
      ->where('bank_id', $data['bank'])
      ->withTrashed()
      ->first();
    if (!$account) {
      $account = Account::create($data);
      event(new CreatedAccount($account));
    } else {
      $account->restore();
      event(new RestoredAccount($account));
    }
    return response('account created', Response::HTTP_CREATED);
  }

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
   * @return Model
   */
  public function link(User $user, Request $request)
  {
    $data = $request->validate([
      'bank_id' => ['required'],
      'number' => ['required', 'numeric'],
      'type' => []
    ]);
    $account = Account::where('bank_id', $data['bank_id'])
      ->where('number', $data['number'])
      ->where('type', $data['type'])
      ->withTrashed()
      ->first();
    if (!$account) {
      $account = Account::create($data);
    } else {
      $account->restore();
    }
    return $user->accounts()->save($account);
  }

  /**
   * @return Builder[]|Collection
   */
  public function indexBase()
  {
    $banks = Bank::select('id')->where('currency_id', config('app.base_currency_id'))->pluck('id');
    return Account::with(['bank', 'owners'])
      ->whereIn('bank_id', $banks)
      ->where('is_operator', true)
      ->get()
      ->append('balance');
  }

  public function update(Account $account, Request $request)
  {
    $data = $request->validate([
      'bank_id' => ['required'],
      'number' => ['required', 'numeric'],
      'type' => []
    ]);
    $account->update($data);
    return $account;
  }

  /**
   * @param Account $account
   * @param User $user
   * @return Application|ResponseFactory|\Illuminate\Http\Response
   * @throws Exception
   */
  public function unlink(Account $account, User $user)
  {
    $account->owners()->detach($user->id);
    if ($account->owners->count() == 0) {
      $account->delete();
    }
    return response('Unlinked Account', 204);
  }

  /**
   * @param Account $account
   * @param User $user
   * @return Application|ResponseFactory|\Illuminate\Http\Response
   * @throws Exception
   */
  public function unBind(Account $account, User $user)
  {
    $account->owners()->detach($user->id);
    return response('Un Bonded Account', 204);
  }

  public function toggleOperatorState(Account $account)
  {
    $account->is_operator = !$account->is_operator;
    return $account->save();
  }
//
}
