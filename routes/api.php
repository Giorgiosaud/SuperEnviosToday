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
    //Public Routes
    Route::post('deploy', 'UtilController@deploy');
    Route::get('last_rate/{id}', 'RateController@lastRate')->name('last_rate');
    Route::post('attachment','AttachmentController@save');

    // Privated Routes Only Auth
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('my_info', 'UserController@info');
        Route::patch('my_info', 'UserController@infoPatch');

    });
    // Privated Routes as Coordinator or Foreign Operator
    Route::group(['middleware'=>['auth:api','role:coordinator,foreign_operator']],function(){
        Route::get('country_banks/{currency}', 'BankController@country_index')->name('country_banks');
        Route::get('operadores-venezuela', 'operatorsController@venezuelanIndex')->name('venezuelan_operators_api');
        Route::get('user_data', 'UserController@userData');
        Route::post('accounts', 'AccountController@store')->name('save_account');
        Route::post('add-transaction', 'TransactionController@normalstore')->name('save_transaction');
        Route::get('foreign_currencies', 'CurrencyController@foreignIndex')->name('foreign_currencies');

    });
    // Privated Routes as Coordinator Foreign Operator or Venezuelan Operator

    Route::group(['middleware' => ['auth:api', 'role:coordinator,foreign_operator,venezuelan_operator']], function () {
    });
    // Privated Routes as Coordinator
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
        Route::get('foreign_operators','UserController@foreignOperators')->name('foreign_operators');
        Route::get('operators','UserController@operators')->name('operators');
        Route::post('currencies', 'CurrencyController@store')->name('create_currency');
        Route::get('banks', 'BankController@index')->name('banks');
        Route::get('venezuelan_banks', 'BankController@venezuelan_index')->name('venezuelan_banks');
        Route::post('banks', 'BankController@store')->name('save_bank');
        Route::post('operator-account', 'AccountController@storeOperatorAccount')->name('save_operator_account');
        Route::post('add-money-venezuela', 'TransactionController@store')->name('add_money_to_venezuela');
        //TODO settings test
        Route::get('pending-transactions','PendingTransactionController@indexAPI')->name('all-pending-transactions');
        Route::get('my-pending-transactions','PendingTransactionController@myTransactionsAPI')->name('my-pending-transactions');
        Route::get('settings','SettingsController@all')->name('all-settings');
        Route::post('setting_tax','SettingsController@setTax')->name('set-tax');
    });
