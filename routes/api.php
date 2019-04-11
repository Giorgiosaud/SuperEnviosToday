<?php

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
    Route::get('test',function(){
        return response([
            'success' => true,
            'message' => 'Changes'
        ], 201);
    });
    Route::post('deploy', 'UtilController@deploy');
    Route::get('last_rate/{id}', 'RateController@lastRate')->name('last_rate');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('my_info', 'UserController@info');
        Route::patch('my_info', 'UserController@infoPatch');
    });
    Route::group(['middleware' => ['auth:api', 'role:coordinator,foreign_operator']], function () {
        Route::get('operadores-venezuela', 'operatorsController@venezuelanIndex')->name('venezuelan_operators');
        //TODO make test
        Route::get('user_data', 'UserController@userData');
        Route::post('add-transaction', 'TransactionController@normalstore');
        Route::post('attachment','AttachmentController@save');

    });
    Route::group(['middleware' => ['auth:api', 'role:coordinator']], function () {
        Route::get('valid', 'AuthController@isValid');
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::patch('user/{user}', 'UserController@patch');
        Route::get('users', 'UserController@apiIndex');
        Route::get('roles', 'RolesController@index');
        Route::post('registerMember', 'RegisterCompanyMembersController@save');
        Route::post('registerClient', 'RegisterCompanyMembersController@saveClient');
        Route::post('login', 'AuthController@login');
        Route::post('signup', 'AuthController@signup');
        Route::get('rates', 'RateController@allRates')->name('rates');
        Route::post('rate', 'RateController@store')->name('create_rate');
        Route::delete('rate/{rate}', 'RateController@destroy')->name('delete_rate');
        Route::patch('rate/{rate}', 'RateController@update')->name('edit_rate');
        Route::get('currencies', 'CurrencyController@index')->name('currencies');
        Route::get('foreign_currencies', 'CurrencyController@foreignIndex')->name('foreign_currencies');
        Route::get('foreign_operators','UserController@foreignOperators')->name('foreign_operators');
        Route::get('operators','UserController@operators')->name('operators');
        Route::post('currencies', 'CurrencyController@store')->name('create_currency');
        //TODO TEST banks
        Route::get('banks', 'BankController@index')->name('banks');
        Route::get('country_banks/{currency}', 'BankController@country_index')->name('country_banks');
        Route::get('venezuelan_banks', 'BankController@venezuelan_index')->name('venezuelan_banks');
        Route::post('banks', 'BankController@store');
        Route::post('accounts', 'AccountController@store');
        Route::post('operator-account', 'AccountController@storeOperatorAccount');
        //TODO TEST and rename next two lines
        Route::post('transaction-to-venezuelan-operator', 'TransactionController@store');

    });
