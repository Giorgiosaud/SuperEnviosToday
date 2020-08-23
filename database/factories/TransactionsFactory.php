<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use App\User;
use Faker\Generator as Faker;

$factory->define(\App\Transaction::class, function (Faker $faker) {
    return [
        'account_id' => function () {
            $account = factory(Account::class)->create();
            return $account->id;
        },
        'client_id' => $faker->randomElement([function () {
            return factory(User::class)->create()->id;
        }, null]),
        'operator_id' => function () {
            return factory(User::class)->create()->id;
        },
        'track_number' => $faker->randomElement([null, 0]),
        'bank_reference' => $faker->numberBetween(1000, 1000000),
        'amount' => $faker->numberBetween(0, 1000000),
        'status' => $faker->randomElement(['executed', 'pending', 'in-progress']),
    ];
});
