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
Route::get('/','HomeController@index')->name('loginForm');

//Route::get('token','Auth\LoginController@token');
//Route::get('get-token','Auth\LoginController@getToken');
Route::group(['middleware' => ['auth']], function () {

    Route::resource('users', 'UserController');
    Route::resource('pending-transactions', 'PendingTransactionsController');
});
