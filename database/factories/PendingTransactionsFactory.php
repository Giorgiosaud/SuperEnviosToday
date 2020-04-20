<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use App\Model;
use App\Transaction;
use App\User;
use Faker\Generator as Faker;

$factory->define(\App\PendingTransaction::class, function (Faker $faker) {
    return [
        'client_id' => function () {
            return factory(User::class)->create()->id;
        },
        'foreign_account_id' => function () {
            return factory(Account::class)->create()->id;
        },
        'receiver_account_id' => function () {
            return factory(Account::class)->create()->id;
        },
        'venezuelan_operator_account_id' => function () {
            return factory(Account::class)->create()->id;
        },
        'rate'   => $faker->randomFloat(4, 4, 5),
        'amount' => $faker->numberBetween(0, 1000000),
        'status' => $faker->randomElement(['pending', 'aprooved', 'rejected']),
        'foreign_id'=>function(){
            return factory(User::class)->create()->id;
        },
        'venezuelan_operator_id'=>function(){
            return factory(User::class)->create()->id;
        },
        'receiver_id'=>function(){
            return factory(User::class)->create()->id;
        },
        'transaction_number'=>function(){
            return factory(Transaction::class)->create()->id;
        }
    ];
});
