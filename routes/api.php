<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

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
Route::post('deploy', 'UtilController@deploy');
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('valid', 'AuthController@isValid');
    Route::get('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::patch('user/{user}','UserController@patch');
    Route::get('users', 'UserController@apiIndex');
    Route::get('roles', 'RolesController@index');
    Route::post('registerMember','RegisterCompanyMembersController@save');
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('rates', 'RateController@allRates');
    Route::get('last_rate', 'RateController@lastRate');
});
