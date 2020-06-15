<?php

/** @var Factory $factory */

use App\Account;
use App\Bank;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->state(Account::class, 'american', function ($faker) {
    return [
        'bank_id' => function () {
            $bank = factory(App\Bank::class)->states('american')->create();
            return $bank->id;
        },
        'type' => $faker->randomElement(['corriente','ahorro',null]),
        'number'              => $faker->bankAccountNumber(),
        'is_operator' => $faker->boolean(),
    ];
});
$factory->state(Account::class, 'chilean', function ($faker) {
    return [
        'bank_id' => function () {
            $bank = factory(App\Bank::class)->states('chilean')->create();
            return $bank->id;
        },
        'type' => $faker->randomElement(['corriente','ahorro']),
        'number'              => $faker->bankAccountNumber(),
        'is_operator' => $faker->boolean(),
    ];
});
$factory->state(Account::class, 'venezuelan', function ($faker) {
    return [
        'bank_id' => function () {
            $bank = factory(App\Bank::class)->states('venezuelan')->create();
            return $bank->id;
        },
        'type' => $faker->randomElement(['corriente','ahorro']),
        'number'              => $faker->bankAccountNumber(),
        'is_operator' => $faker->boolean(),
    ];
});

$factory->define(Account::class, function (Faker $faker) {
    return [
        'bank_id' => function () {
            $bank = factory(Bank::class)->create();
            return $bank->id;
        },
        'type' => $faker->randomElement(['corriente','ahorro']),
        'number'              => $faker->bankAccountNumber(),
        'is_operator' => $faker->boolean(),
    ];
});
