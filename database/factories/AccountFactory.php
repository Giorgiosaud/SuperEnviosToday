<?php

use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    return [
        'bank_id' => function () {
            $bank = factory(\App\Bank::class)->create();
            return $bank->id;
        },
        'type' => $faker->randomElement(['corriente','ahorro']),
        'number'              => $faker->bankAccountNumber(),
        'is_operator_account' => $faker->boolean(),
    ];
});
