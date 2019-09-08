<?php

use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    return [
        'bank_id' => function () {
            $bank = factory(\App\Bank::class)->create();

            return $bank->id;
        },
        'is_operator_account' => $faker->boolean(),
        'number'              => $faker->bankAccountNumber(),
    ];
});
