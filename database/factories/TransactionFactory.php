<?php

use Faker\Generator as Faker;

$factory->define(
    App\Transaction::class,
    function (Faker $faker) {
        return [
            'amount' => $faker->numberBetween(0, 1000000),
            'to_account_id' => function () {
                $account = factory(\App\Account::class)->create();
                return $account->id;
            },
            'from_account_id' => $faker->randomElement([function () {
                $account = factory(\App\Account::class)->create();
                return $account->id;
            }, null])
        ];
    }
);
