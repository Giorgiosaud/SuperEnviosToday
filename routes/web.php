<?php

use Illuminate\Contracts\Auth\Guard;

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
Route::get('/user_token', function (Request $request,Guard $guard) {
   return $guard->user()->tokens->last();

    return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                    ->toDateTimeString(),
        ]);
});
Route::group(['prefix' => 'coordinator'], function () {
    Route::get('/createMember','RegisterCompanyMembersController@create')->middleware('auth','role:coordinator')->name('registerOperator');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
