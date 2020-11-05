<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/
Route::group(['middleware' => [
    'auth:api'
], 'as' => 'api.'], function () {
  Route::post('user/save',['uses'=>'Api\UserController@store', 'as'=>'api.user.store']);
  Route::apiResource('pending-transaction', 'Api\PendingTransactionController', ['only' => ['index', 'update']])->middleware('role:coordinator foreign_operator');
  Route::get('my-pending-transaction',['uses'=> 'Api\PendingTransactionController@myIndex','as'=>'api.my-pending-transactions.index'])->middleware('role:coordinator foreign_operator');
  Route::apiResource('user', 'Api\UserController', ['only' => ['index', 'update']])->middleware('role:coordinator foreign_operator');
  
  Route::get('users/operators', ['uses' => 'Api\UserController@getOperators', 'as' => 'users.operators.index'])->middleware('role:coordinator foreign_operator');
  Route::post('user/verify_email', ['uses' => 'Api\UserController@resendVerificationEmail', 'as' => 'user.resend'])->middleware('role:coordinator foreign_operator');
  Route::get('user/receivers/{user}', ['uses' => 'Api\UserController@receivers', 'as' => 'user.receivers'])->middleware('role:coordinator foreign_operator');
  Route::post('user/{user}/receiver', ['uses' => 'Api\UserController@createReceiver', 'as' => 'user.create.receiver'])->middleware('role:coordinator foreign_operator');
  Route::patch('user/{client}/receiver/{receiver}/unlink', ['uses' => 'Api\UserController@unlink', 'as' => 'receiver.unlink'])->middleware('role:coordinator foreign_operator');
  Route::get('user/{idnType}/{idn}', ['uses' => 'Api\UserController@search', 'as' => 'user.search'])->middleware('role:coordinator foreign_operator');

  Route::get('currency/foreign', ['uses' => 'Api\CurrencyController@foreign', 'as' => 'currency.foreign'])->middleware('role:coordinator foreign_operator');
  Route::apiResource('currencies', 'Api\CurrencyController', ['only' => ['index', 'store', 'destroy', 'update']]);
  Route::apiResource('settings', 'Api\SettingController', ['only' => ['index', 'store', 'destroy', 'update']])->middleware('role:coordinator');;
  Route::apiResource('rates', 'Api\RateController', ['only' => ['index', 'store', 'destroy', 'update']]);
  Route::get('banks/base', ['uses' => 'Api\BankController@baseBanks', 'as' => 'index.banks.venezuelan']);
  Route::apiResource('banks', 'Api\BankController', ['only' => ['index', 'store', 'destroy', 'update']]);
  //TODO review permmissions go on
  Route::get('accounts', ['uses' => 'Api\AccountController@index', 'as' => 'index.accounts']);
  Route::post('accounts', ['uses' => 'Api\AccountController@store', 'as' => 'store.accounts'])->middleware('role:coordinator foreign_operator');
  Route::delete('accounts/{account}', ['uses' => 'Api\AccountController@destroy', 'as' => 'destroy.accounts'])->middleware('role:coordinator');
  Route::patch('account/{account}', ['uses' => 'Api\AccountController@update', 'as' => 'account.update'])->middleware('role:coordinator foreign_operator');
  Route::patch('account/{account}/toggle-operator-state', ['uses' => 'Api\AccountController@toggleOperatorState', 'as' => 'account.toggle.operator.state'])->middleware('role:coordinator');;
  Route::get('accounts/base', ['uses' => 'Api\AccountController@indexBase', 'as' => 'index.base.accounts']);
  Route::get('accounts/{currencyId}', ['uses' => 'Api\AccountController@getAccounts', 'as' => 'accounts.from_currency']);
  Route::patch('account/{account}/user/{user}/unlink', ['uses' => 'Api\AccountController@unlink', 'as' => 'account.unlink'])->middleware('role:coordinator foreign_operator');
  Route::patch('account/{account}/user/{user}/unbind', ['uses' => 'Api\AccountController@unbind', 'as' => 'account.unbind'])->middleware('role:coordinator foreign_operator');
  Route::post('accounts/link/{user}', ['uses' => 'Api\AccountController@link', 'as' => 'accounts.link'])->middleware('role:coordinator foreign_operator');
  Route::get('rate/{currencyId}', ['uses' => 'Api\RateController@get', 'as' => 'rate.get']);
  Route::post('file/upload', ['uses' => 'Api\AttachmentController@upload', 'as' => 'file.upload'])->middleware('role:coordinator foreign_operator venezuelan_operator');
  Route::get('transaction/verify/{transactionNumber}', ['uses' => 'Api\TransactionController@verify', 'as' => 'transaction.verify']);
  Route::post('transaction/execute', ['uses' => 'Api\TransactionController@execute', 'as' => 'transaction.execute'])->middleware('role:coordinator foreign_operator');
  Route::get('transactions', ['uses' => 'Api\TransactionController@index', 'as' => 'transactions.index']);
  Route::post('transaction/{account}/', ['uses' => 'Api\TransactionController@create', 'as' => 'transactions.create'])->middleware('role:coordinator foreign_operator');
  Route::get('my-transactions', ['uses' => 'Api\TransactionController@myIndex', 'as' => 'my.transactions.index'])->middleware('role:coordinator foreign_operator');
  Route::get('my-venezuelan-transactions', ['uses' => 'Api\TransactionController@myVenezuelanIndex', 'as' => 'my.venezuelan-transactions.index']);
  Route::get('related-venezuelan-transactions/{transaction}', ['uses' => 'Api\TransactionController@relatedVenezuelanTransactions', 'as' => 'venezuelan.transactions.index']);
  Route::get('my-related-transactions/{transaction}', ['uses' => 'Api\TransactionController@myRelatedTransactions', 'as' => 'my.own.transactions.index']);
  Route::patch('related-venezuelan-transaction', ['uses' => 'Api\TransactionController@updateRelatedVenezuelanTransaction', 'as' => 'venezuelan.transactions.update'])->middleware('role:coordinator foreign_operator venezuelan_operator');
  Route::delete('attachment/{attachment}',['uses'=>'Api\AttachmentController@destroy','as'=>'attachment.destroy'])->middleware('role:coordinator foreign_operator venezuelan_operator');
});
