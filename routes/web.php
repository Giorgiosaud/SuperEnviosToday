<?php
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
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', 'UserController@myProfile')->name('user_profile');
    });
    Route::middleware(['auth', 'role:foreign_operator,coordinator'])->group(function () {
        Route::get('/transactions', 'TransactionController@index')->name('chilean_transactions');
        Route::get('/transactions/pending', 'TransactionController@pending')->name('chilean_pending_transactions');
        //TODO agregar endpoint de transferencias Realizadas
    });
    Route::middleware(['auth', 'role:coordinator'])->group(function () {
        Route::get('/createMember', 'RegisterCompanyMembersController@create')->name('registerOperator');
        Route::get('/registerOperatorAccount', 'RegisterCompanyMembersController@createAndAssignAccount')->name('registerOperatorAccount');
        Route::get('/users', 'UserController@index')->name('users');
        Route::get('/rate', 'RateController@index')->name('rate');
        Route::get('/settings', 'SettingsController@index')->name('settings');
        Route::get('/add_funds', 'operatorsController@index')->name('addFoundsToVenezuelanOperator');
    });

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
