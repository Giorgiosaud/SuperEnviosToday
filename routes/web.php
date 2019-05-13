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
    Auth::routes();
    Route::get('/access_token', 'AuthController@getToken');
    Route::middleware(['auth'])->group(function () {

        Route::get('/profile', 'UserController@myProfile')->name('user_profile');
      Route::get('/change_password', 'UserController@changePassword')->name('change_password');
      Route::post('/update_password', 'UserController@updatePassword')->name('update_password');
    });
    Route::middleware(['auth', 'role:venezuelan_operator,coordinator'])->group(function () {
        Route::get('venezuelan_transactions', 'VenezuelanTransactionController@index')->name('venezuelan_transactions');
    });
    Route::middleware(['auth', 'role:foreign_operator,coordinator'])->group(function () {
        Route::get('/transactions', 'TransactionController@index')->name('make_transaction');
      Route::get('/transactions/status', 'TransactionController@list')->name('my_transactions');
        Route::get('/transactions/pending', 'PendingTransactionController@myTransactions')->name('chilean_pending_transactions');
        Route::get('/venezuelan_operators', 'operatorsController@venezuelanList')->name('venezuelan_operators');
        //TODO create foreign operator list
    });
    Route::middleware(['auth', 'role:coordinator'])->group(function () {
        Route::get('/aliasing/{user}','userController@aliasify')->name('aliasing');
        Route::get('/createMember', 'RegisterCompanyMembersController@create')->name('registerOperator');
        Route::get('/registerOperatorAccount', 'RegisterCompanyMembersController@createAndAssignAccount')->name('registerOperatorAccount');
        Route::get('/users', 'UserController@index')->name('users');
        Route::get('/rate', 'RateController@index')->name('rate');
        Route::get('/settings', 'SettingsController@index')->name('settings');
        Route::get('/add_funds', 'operatorsController@index')->name('addFoundsToVenezuelanOperator');
        Route::get('/pending_transactions', 'PendingTransactionController@index')->name('pending_operations');
        Route::get('/foreign_operators', 'operatorsController@foreignList')->name('foreign_operators');
        Route::get('/coordinator_transaction', 'TransactionController@addTransaction')->name('coordinator_transaction');
      Route::get('/list_transaction', 'TransactionController@listTransactions')->name('transactions_list');
      Route::get('/fix_transaction', 'TransactionController@fixTransaction')->name('transactions_fix');
    });


    Route::get('/home', 'HomeController@index')->name('home');
