<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);
Route::get('/','HomeController@index')->name('loginForm')->middleware('auth');
Route::get('/gracias','HomeController@thanks')->name('auth.thanks');

//Route::get('token','Auth\LoginController@token');
//Route::get('get-token','Auth\LoginController@getToken');
Route::group(['middleware' => ['auth']], function () {

    Route::resource('users', 'UserController',['except'=>['destroy','create','store','edit']]);
    Route::get('banks', ['uses'=>'BankController@index','as'=>'banks.index']);
    Route::get('currencies', ['uses'=>'CurrencyController@index','as'=>'currencies.index']);
    Route::get('rates', ['uses'=>'RateController@index','as'=>'rates.index']);
    Route::get('settings', ['uses'=>'SettingController@index','as'=>'settings.index']);
    Route::get('users/login-as/{user}',['uses'=>'UserController@loginAs','as'=>'user.login.as']);
    Route::resource('pending-transactions', 'PendingTransactionController',['only'=>['index']]);
    Route::get('transaction/create', ['uses'=>'TransactionController@create','as'=>'transaction.create']);
    Route::get('transactions', ['uses'=>'TransactionController@index','as'=>'transaction.index']);
});
