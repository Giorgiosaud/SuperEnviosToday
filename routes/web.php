<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/access_token', 'AuthController@getToken');

Route::middleware(['auth','role:chilean_operator'])->group( function () {
  Route::get('/transactions','TransactionController@index')->name('chilean_transactions');
    Route::get('/transactions/pending','TransactionController@pending')->name('chilean_pending_transactions');
});
Route::middleware(['auth','role:coordinator'])->group( function () {
    Route::get('/createMember','RegisterCompanyMembersController@create')->name('registerOperator');
    Route::get('/users','UserController@index')->name('users');
    Route::get('/rate','RateController@index')->name('rate');
    Route::get('/settings','SettingsController@index')->name('settings');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
