<?php

use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    return [
        'bank_id' => function () {
            $bank = factory(\App\Bank::class)->create();

            return $bank->id;
        },
        'user_id' => function () {
            $user = factory(\App\User::class)->create();

            return $user->id;
        },
        'currency_id' => function () {
            $user = factory(\App\Currency::class)->create();

            return $user->id;
        },

        'is_operator_account' => $faker->boolean(),
        'number'              => $faker->bankAccountNumber(),
    ];
});
