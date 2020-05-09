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
    Route::apiResource('users','Api\UserController',['only'=>['index']]);
    Route::get('user/{idnType}/{idn}',['uses'=>'Api\UserController@search','as'=>'users.search']);
});
