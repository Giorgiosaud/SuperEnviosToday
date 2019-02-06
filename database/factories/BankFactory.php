<?php

use Faker\Generator as Faker;

$factory->define(App\Bank::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Orinoco', 'Caroni', 'Banesco', 'Provincia', 'Santander']),
        'currency_id'=>
            function () {
                $currency = factory(\App\Currency::class)->create();
                return $currency->id;
        }
    ];
});
