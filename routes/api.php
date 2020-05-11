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
Route::group(['middleware' => ['auth:api'],'as'=>'api.'], function () {
    Route::apiResource('pending-transaction','Api\PendingTransactionController',[
        'only'=>['index','update']
    ]);
    Route::apiResource('user','Api\UserController',['only'=>['index','store']]);
    Route::post('user/verify_email',['uses'=>'Api\UserController@resendVerificationEmail','as'=>'user.resend']);
    Route::get('user/{idnType}/{idn}',['uses'=>'Api\UserController@search','as'=>'user.search']);
    Route::apiResource('currency','Api\CurrencyController',['only'=>['index']]);
    Route::get('currency/foreign',['uses'=>'Api\CurrencyController@foreign','as'=>'currency.foreign']);
    Route::get('accounts/{currencyId}',['uses'=>'Api\AccountController@getAccounts','as'=>'accounts.from_currency']);
    Route::get('rate/{currencyId}',['uses'=>'Api\RateController@get','as'=>'rate.get']);
    Route::post('file/upload', ['uses'=>'Api\AttachmentController@upload','as'=>'file.upload']);
});
