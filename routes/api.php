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
Route::group(['middleware' => ['auth:api'], 'as' => 'api.'], function () {
    Route::apiResource('pending-transaction', 'Api\PendingTransactionController', [
        'only' => ['index', 'update']
    ]);
    Route::patch('user/{client}/receiver/{receiver}/unlink', ['uses' => 'Api\UserController@unlink', 'as' => 'receiver.unlink']);
    Route::apiResource('user', 'Api\UserController', ['only' => ['index', 'store', 'update']]);
    Route::get('users/operators', ['uses'=>'Api\UserController@getOperators','as'=>'users.operators.index']);
    Route::post('user/verify_email', ['uses' => 'Api\UserController@resendVerificationEmail', 'as' => 'user.resend']);
    Route::get('user/receivers/{user}', ['uses' => 'Api\UserController@receivers', 'as' => 'user.receivers']);
    Route::post('user/{user}/receiver', ['uses' => 'Api\UserController@createReceiver', 'as' => 'user.create.receiver']);
    Route::get('user/{idnType}/{idn}', ['uses' => 'Api\UserController@search', 'as' => 'user.search']);
    Route::get('currency/foreign', ['uses' => 'Api\CurrencyController@foreign', 'as' => 'currency.foreign']);
    Route::apiResource('currencies', 'Api\CurrencyController', ['only' => ['index', 'store', 'destroy', 'update']]);
    Route::apiResource('settings', 'Api\SettingController', ['only' => ['index', 'store', 'destroy', 'update']]);
    Route::apiResource('rates', 'Api\RateController', ['only' => ['index', 'store', 'destroy', 'update']]);
    Route::get('banks/base', ['uses' => 'Api\BankController@baseBanks', 'as' => 'index.banks.venezuelan']);
    Route::apiResource('banks', 'Api\BankController', ['only' => ['index', 'store', 'destroy', 'update']]);
    Route::get('accounts', ['uses' => 'Api\AccountController@index', 'as' => 'index.accounts']);
    Route::post('accounts', ['uses' => 'Api\AccountController@store', 'as' => 'store.accounts']);
    Route::patch('account/{account}', ['uses' => 'Api\AccountController@update', 'as' => 'account.update']);
    Route::get('accounts/base', ['uses' => 'Api\AccountController@indexBase', 'as' => 'index.base.accounts']);
    Route::get('accounts/{currencyId}', ['uses' => 'Api\AccountController@getAccounts', 'as' => 'accounts.from_currency']);
    Route::patch('account/{account}/user/{user}/unlink', ['uses' => 'Api\AccountController@unlink', 'as' => 'account.unlink']);
    Route::patch('account/{account}/user/{user}/unbind', ['uses' => 'Api\AccountController@unbind', 'as' => 'account.unbind']);
    Route::post('accounts/link/{user}', ['uses' => 'Api\AccountController@link', 'as' => 'accounts.link']);
    Route::get('rate/{currencyId}', ['uses' => 'Api\RateController@get', 'as' => 'rate.get']);
    Route::post('file/upload', ['uses' => 'Api\AttachmentController@upload', 'as' => 'file.upload']);
    Route::get('transaction/verify/{transactionNumber}', ['uses' => 'Api\TransactionController@verify', 'as' => 'transaction.verify']);
    Route::post('transaction/execute', ['uses' => 'Api\TransactionController@execute', 'as' => 'transaction.execute']);
    Route::get('transactions', ['uses' => 'Api\TransactionController@index', 'as' => 'transactions.index']);
    Route::get('my-transactions', ['uses' => 'Api\TransactionController@myIndex', 'as' => 'my.transactions.index']);
});
