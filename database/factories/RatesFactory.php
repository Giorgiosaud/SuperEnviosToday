<?php

use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'currency_id' => function () {
            $currency = factory(\App\Currency::class)->create();

            return $currency->id;
        },
        'since' => $faker->dateTime(),
        'amount_bs' => $faker->numberBetween(300,6000),

    ];
});
